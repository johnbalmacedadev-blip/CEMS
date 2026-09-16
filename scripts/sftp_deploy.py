#!/usr/bin/env python3
"""SFTP deploy helper for CEMS (preferred over FTP on this host)."""

from __future__ import annotations

import argparse
import os
import posixpath
import sys
from pathlib import Path

import paramiko

ROOT = Path(__file__).resolve().parents[1]
ENV_PATH = ROOT / ".env.deploy"

SKIP_NAMES = {".env", ".env.backup", ".env.deploy", ".git"}
SKIP_PREFIXES = (".git/",)


def load_env(path: Path) -> dict[str, str]:
    out: dict[str, str] = {}
    for raw in path.read_text(encoding="utf-8").splitlines():
        line = raw.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, val = line.split("=", 1)
        key = key.strip()
        val = val.strip()
        if (val.startswith('"') and val.endswith('"')) or (val.startswith("'") and val.endswith("'")):
            val = val[1:-1]
        out[key] = val
    return out


def connect(cfg: dict[str, str]) -> paramiko.SFTPClient:
    host = cfg.get("SFTP_HOST") or cfg.get("FTP_HOST") or ""
    port = int(cfg.get("SFTP_PORT") or "22")
    user = cfg.get("SFTP_USER") or cfg.get("FTP_USER") or ""
    password = cfg.get("SFTP_PASSWORD") or cfg.get("FTP_PASSWORD") or ""
    key_path = cfg.get("SFTP_KEY_PATH") or ""

    if not host or not user:
        raise SystemExit("SFTP/FTP host and user required in .env.deploy")

    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    kwargs = {
        "hostname": host,
        "port": port,
        "username": user,
        "timeout": 30,
        "allow_agent": False,
        "look_for_keys": False,
    }
    if key_path:
        kwargs["key_filename"] = key_path
    else:
        kwargs["password"] = password

    print(f"Connecting SFTP {user}@{host}:{port} ...")
    client.connect(**kwargs)
    sftp = client.open_sftp()
    sftp._cems_ssh = client  # type: ignore[attr-defined]
    return sftp


def close(sftp: paramiko.SFTPClient) -> None:
    ssh = getattr(sftp, "_cems_ssh", None)
    sftp.close()
    if ssh is not None:
        ssh.close()


def ensure_dir(sftp: paramiko.SFTPClient, remote: str) -> None:
    remote = remote.replace("\\", "/").rstrip("/")
    if remote in ("", ".", "/"):
        return
    parts = [p for p in remote.split("/") if p and p != "."]
    cur = ""
    # Support absolute and relative
    absolute = remote.startswith("/")
    for part in parts:
        cur = f"{cur}/{part}" if cur else (f"/{part}" if absolute else part)
        try:
            sftp.stat(cur)
        except FileNotFoundError:
            sftp.mkdir(cur)


def should_skip(rel: str) -> bool:
    rel = rel.replace("\\", "/")
    top = rel.split("/", 1)[0]
    if top in SKIP_NAMES or Path(rel).name in SKIP_NAMES:
        return True
    return any(rel.startswith(p) for p in SKIP_PREFIXES)


def upload_tree(sftp: paramiko.SFTPClient, local_dir: Path, remote_dir: str) -> int:
    local_dir = local_dir.resolve()
    remote_dir = remote_dir.replace("\\", "/").strip("/") or "."
    count = 0
    for path in local_dir.rglob("*"):
        rel = path.relative_to(local_dir).as_posix()
        if should_skip(rel):
            continue
        remote_path = rel if remote_dir == "." else f"{remote_dir}/{rel}"
        if path.is_dir():
            ensure_dir(sftp, remote_path)
            continue
        ensure_dir(sftp, posixpath.dirname(remote_path))
        sftp.put(str(path), remote_path)
        count += 1
        if count % 50 == 0:
            print(f"  uploaded {count} files...")
    print(f"  uploaded {count} files total.")
    return count


def put_env_if_missing(sftp: paramiko.SFTPClient, local_env: Path, remote_dir: str) -> None:
    remote_dir = remote_dir.replace("\\", "/").strip("/") or "."
    remote_env = ".env" if remote_dir == "." else f"{remote_dir}/.env"
    try:
        sftp.stat(remote_env)
        print(f"Remote {remote_env} exists — not overwritten.")
        return
    except FileNotFoundError:
        pass
    ensure_dir(sftp, posixpath.dirname(remote_env) if remote_dir != "." else ".")
    sftp.put(str(local_env), remote_env)
    print(f"Uploaded first-run .env to {remote_env}")


def main() -> int:
    if not ENV_PATH.is_file():
        print("Missing .env.deploy", file=sys.stderr)
        return 1
    cfg = load_env(ENV_PATH)
    remote = (cfg.get("REMOTE_PATH") or ".").strip() or "."

    parser = argparse.ArgumentParser()
    parser.add_argument("command", choices=["test", "list", "upload", "put-env"])
    parser.add_argument("arg1", nargs="?")
    parser.add_argument("arg2", nargs="?")
    args = parser.parse_args()

    sftp = connect(cfg)
    try:
        if args.command == "test":
            pwd = sftp.normalize(".")
            print(f"OK SFTP connected. PWD={pwd}")
            names = sftp.listdir(".")
            print(f"Entries ({len(names)}):")
            for name in names[:50]:
                print(f"  {name}")
            return 0

        if args.command == "list":
            path = args.arg1 or remote
            for name in sftp.listdir(path):
                print(name)
            return 0

        if args.command == "upload":
            local = Path(args.arg1 or "")
            rem = args.arg2 or remote
            if not local.is_dir():
                print("Usage: sftp_deploy.py upload <localDir> [remotePath]", file=sys.stderr)
                return 1
            print(f"Uploading {local} -> {rem}")
            upload_tree(sftp, local, rem)
            return 0

        if args.command == "put-env":
            local_env = Path(args.arg1 or "")
            if not local_env.is_file():
                print("Usage: sftp_deploy.py put-env <localEnvFile>", file=sys.stderr)
                return 1
            put_env_if_missing(sftp, local_env, remote)
            return 0
    finally:
        close(sftp)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
