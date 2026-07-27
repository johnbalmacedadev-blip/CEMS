<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Car Empire Management System API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://localhost";
        var useCsrf = Boolean(1);
        var csrfUrl = "/home";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.11.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.11.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-unit-report" class="tocify-header">
                <li class="tocify-item level-1" data-unique="unit-report">
                    <a href="#unit-report">Unit Report</a>
                </li>
                                    <ul id="tocify-subheader-unit-report" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="unit-report-GETvehicles-search-archiveable">
                                <a href="#unit-report-GETvehicles-search-archiveable">Search vehicles that can be moved to Archived (for the Archived tab modal).</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="unit-report-POSTvehicles--vehicle_id--archive">
                                <a href="#unit-report-POSTvehicles--vehicle_id--archive">Archive a vehicle</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-live-team-chat" class="tocify-header">
                <li class="tocify-item level-1" data-unique="live-team-chat">
                    <a href="#live-team-chat">Live Team Chat</a>
                </li>
                                    <ul id="tocify-subheader-live-team-chat" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="live-team-chat-GETapi-chat-sync">
                                <a href="#live-team-chat-GETapi-chat-sync">Sync chat messages</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="live-team-chat-POSTapi-chat-messages">
                                <a href="#live-team-chat-POSTapi-chat-messages">Send a chat message</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="live-team-chat-POSTapi-chat-heartbeat">
                                <a href="#live-team-chat-POSTapi-chat-heartbeat">Chat presence heartbeat</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-expenses" class="tocify-header">
                <li class="tocify-item level-1" data-unique="expenses">
                    <a href="#expenses">Expenses</a>
                </li>
                                    <ul id="tocify-subheader-expenses" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="expenses-GETapi-expenses-vehicles-search">
                                <a href="#expenses-GETapi-expenses-vehicles-search">Search vehicles for expense autocomplete</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-other-endpoints" class="tocify-header">
                <li class="tocify-item level-1" data-unique="other-endpoints">
                    <a href="#other-endpoints">Other Endpoints</a>
                </li>
                                    <ul id="tocify-subheader-other-endpoints" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="other-endpoints-GETapi-user">
                                <a href="#other-endpoints-GETapi-user">GET api/user</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-GETapi-makes-search">
                                <a href="#other-endpoints-GETapi-makes-search">GET api/makes/search</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-GETapi-models-search">
                                <a href="#other-endpoints-GETapi-models-search">GET api/models/search</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-GETapi-contracts-vehicles-search">
                                <a href="#other-endpoints-GETapi-contracts-vehicles-search">Search vehicles for contract (autocomplete).</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-GETapi-sales-agent-commissions-agents-search">
                                <a href="#other-endpoints-GETapi-sales-agent-commissions-agents-search">JSON typeahead for commission forms: match name or staff ID code.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-GETapi-expenses-vehicle-categories">
                                <a href="#other-endpoints-GETapi-expenses-vehicle-categories">Get all vehicle expense categories.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-POSTapi-expenses-vehicle-categories">
                                <a href="#other-endpoints-POSTapi-expenses-vehicle-categories">Add a new vehicle expense category.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-GETapi-expenses-payment-methods">
                                <a href="#other-endpoints-GETapi-expenses-payment-methods">Get all active payment methods.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-GETapi-tools-search">
                                <a href="#other-endpoints-GETapi-tools-search">Search for tool names (autocomplete)</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-GETapi-tools-history">
                                <a href="#other-endpoints-GETapi-tools-history">Get purchase history for a specific tool name</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-POSTapi-tools">
                                <a href="#other-endpoints-POSTapi-tools">Store a newly created tool</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-GETapi-tools--id-">
                                <a href="#other-endpoints-GETapi-tools--id-">Display the specified tool</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-PUTapi-tools--id-">
                                <a href="#other-endpoints-PUTapi-tools--id-">Update the specified tool</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-DELETEapi-tools--id-">
                                <a href="#other-endpoints-DELETEapi-tools--id-">Remove the specified tool</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-GETapi-models--make-">
                                <a href="#other-endpoints-GETapi-models--make-">GET api/models/{make}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-GETapi-soa-transactions">
                                <a href="#other-endpoints-GETapi-soa-transactions">Get transactions for a specific payment method and date.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-POSTapi-soa-daily-budget">
                                <a href="#other-endpoints-POSTapi-soa-daily-budget">Store or update daily budget.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-POSTapi-soa-add-cash">
                                <a href="#other-endpoints-POSTapi-soa-add-cash">Add cash (credit) to a payment method for a specific date.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-GETapi-soa-cash-additions">
                                <a href="#other-endpoints-GETapi-soa-cash-additions">Get all cash additions for a payment method and date.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-PUTapi-soa-cash--id-">
                                <a href="#other-endpoints-PUTapi-soa-cash--id-">Update a cash addition (credit).</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-DELETEapi-soa-cash--id-">
                                <a href="#other-endpoints-DELETEapi-soa-cash--id-">Delete a cash addition (credit).</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-PUTapi-soa-update-starting-cash">
                                <a href="#other-endpoints-PUTapi-soa-update-starting-cash">Update starting cash for a daily budget.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-POSTapi-soa-manual-entries">
                                <a href="#other-endpoints-POSTapi-soa-manual-entries">Store a manual SOA line (description + debit or credit) for a date.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-PUTapi-soa-manual-entries--soa_manual_entry_id-">
                                <a href="#other-endpoints-PUTapi-soa-manual-entries--soa_manual_entry_id-">Update a manual SOA line.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-DELETEapi-soa-manual-entries--soa_manual_entry_id-">
                                <a href="#other-endpoints-DELETEapi-soa-manual-entries--soa_manual_entry_id-">Remove a manual SOA line.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-DELETEapi-soa-daily-record">
                                <a href="#other-endpoints-DELETEapi-soa-daily-record">Delete all SOA data for a payment method on a specific date.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-GETapi-soa-floated-funds">
                                <a href="#other-endpoints-GETapi-soa-floated-funds">Floated funds total and line items (declared starting below prior day's closing).</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: July 21, 2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<p>JSON API for Car Empire Management System — autocomplete, chat, expenses, SOA, tools, and vehicle operations.</p>
<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>
<pre><code>These endpoints power dynamic features in CEMS (autocomplete fields, live chat, SOA, etc.).

**Authentication:** Log in to the web app first. Endpoints use Laravel **session cookies**, not API tokens.

&lt;aside&gt;When using &lt;strong&gt;Try It Out&lt;/strong&gt;, keep this docs tab in the same browser where you are logged in to CEMS.&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>This API is not authenticated.</p>

        <h1 id="unit-report">Unit Report</h1>

    

                                <h2 id="unit-report-GETvehicles-search-archiveable">Search vehicles that can be moved to Archived (for the Archived tab modal).</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Returns units in Available, Released, or Forfeited status. Excludes already-archived vehicles.</p>

<span id="example-requests-GETvehicles-search-archiveable">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/vehicles/search-archiveable?q=Toyota" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/vehicles/search-archiveable"
);

const params = {
    "q": "Toyota",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETvehicles-search-archiveable">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">[
    {
        &quot;id&quot;: 1,
        &quot;plate_number&quot;: &quot;ABC 1234&quot;,
        &quot;label&quot;: &quot;2020 Toyota Vios (ABC 1234)&quot;,
        &quot;status&quot;: &quot;Available&quot;,
        &quot;archive_url&quot;: &quot;http://localhost/vehicles/1/archive&quot;
    }
]</code>
 </pre>
    </span>
<span id="execution-results-GETvehicles-search-archiveable" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETvehicles-search-archiveable"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETvehicles-search-archiveable"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETvehicles-search-archiveable" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETvehicles-search-archiveable">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETvehicles-search-archiveable" data-method="GET"
      data-path="vehicles/search-archiveable"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETvehicles-search-archiveable', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETvehicles-search-archiveable"
                    onclick="tryItOut('GETvehicles-search-archiveable');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETvehicles-search-archiveable"
                    onclick="cancelTryOut('GETvehicles-search-archiveable');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETvehicles-search-archiveable"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>vehicles/search-archiveable</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETvehicles-search-archiveable"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETvehicles-search-archiveable"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>q</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="q"                data-endpoint="GETvehicles-search-archiveable"
               value="Toyota"
               data-component="query">
    <br>
<p>optional Search by plate, make, model, or variant. Example: <code>Toyota</code></p>
            </div>
                </form>

                    <h2 id="unit-report-POSTvehicles--vehicle_id--archive">Archive a vehicle</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Moves an Available, Released, or Forfeited unit to Archived status.</p>

<span id="example-requests-POSTvehicles--vehicle_id--archive">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/vehicles/1/archive" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/vehicles/1/archive"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTvehicles--vehicle_id--archive">
            <blockquote>
            <p>Example response (200, JSON request):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Vehicle moved to Archived successfully.&quot;,
    &quot;swal_title&quot;: &quot;Archived&quot;,
    &quot;vehicle_id&quot;: 1
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Not archiveable):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;This vehicle cannot be archived.&quot;,
    &quot;swal_title&quot;: &quot;Cannot Archive&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTvehicles--vehicle_id--archive" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTvehicles--vehicle_id--archive"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTvehicles--vehicle_id--archive"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTvehicles--vehicle_id--archive" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTvehicles--vehicle_id--archive">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTvehicles--vehicle_id--archive" data-method="POST"
      data-path="vehicles/{vehicle_id}/archive"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTvehicles--vehicle_id--archive', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTvehicles--vehicle_id--archive"
                    onclick="tryItOut('POSTvehicles--vehicle_id--archive');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTvehicles--vehicle_id--archive"
                    onclick="cancelTryOut('POSTvehicles--vehicle_id--archive');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTvehicles--vehicle_id--archive"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>vehicles/{vehicle_id}/archive</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTvehicles--vehicle_id--archive"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTvehicles--vehicle_id--archive"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>vehicle_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="vehicle_id"                data-endpoint="POSTvehicles--vehicle_id--archive"
               value="1"
               data-component="url">
    <br>
<p>The ID of the vehicle. Example: <code>1</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>vehicle</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="vehicle"                data-endpoint="POSTvehicles--vehicle_id--archive"
               value="1"
               data-component="url">
    <br>
<p>The vehicle ID. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="live-team-chat">Live Team Chat</h1>

    

                                <h2 id="live-team-chat-GETapi-chat-sync">Sync chat messages</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Poll for new team chat messages and online users. Used by the live chat widget (every 3 seconds).</p>

<span id="example-requests-GETapi-chat-sync">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/chat/sync?after=42" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/chat/sync"
);

const params = {
    "after": "42",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-chat-sync">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;messages&quot;: [],
    &quot;online_users&quot;: [],
    &quot;latest_id&quot;: 0,
    &quot;current_user&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;Admin&quot;,
        &quot;initials&quot;: &quot;A&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-chat-sync" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-chat-sync"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-chat-sync"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-chat-sync" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-chat-sync">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-chat-sync" data-method="GET"
      data-path="api/chat/sync"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-chat-sync', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-chat-sync"
                    onclick="tryItOut('GETapi-chat-sync');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-chat-sync"
                    onclick="cancelTryOut('GETapi-chat-sync');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-chat-sync"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/chat/sync</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-chat-sync"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-chat-sync"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>after</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="after"                data-endpoint="GETapi-chat-sync"
               value="42"
               data-component="query">
    <br>
<p>optional Return only messages with ID greater than this value. Example: <code>42</code></p>
            </div>
                </form>

                    <h2 id="live-team-chat-POSTapi-chat-messages">Send a chat message</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Post a text message, link, or file attachment to the team chat.</p>

<span id="example-requests-POSTapi-chat-messages">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/chat/messages" \
    --header "Content-Type: multipart/form-data" \
    --header "Accept: application/json" \
    --form "body=Good morning team!"\
    --form "link_url=https://example.com"\
    --form "attachment=@C:\Users\VJ Kyle\AppData\Local\Temp\php851B.tmp" </code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/chat/messages"
);

const headers = {
    "Content-Type": "multipart/form-data",
    "Accept": "application/json",
};

const body = new FormData();
body.append('body', 'Good morning team!');
body.append('link_url', 'https://example.com');
body.append('attachment', document.querySelector('input[name="attachment"]').files[0]);

fetch(url, {
    method: "POST",
    headers,
    body,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-chat-messages">
            <blockquote>
            <p>Example response (201):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: {
        &quot;id&quot;: 1,
        &quot;body&quot;: &quot;Hello&quot;,
        &quot;user&quot;: {}
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Enter a message, link, or file.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-chat-messages" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-chat-messages"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-chat-messages"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-chat-messages" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-chat-messages">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-chat-messages" data-method="POST"
      data-path="api/chat/messages"
      data-authed="1"
      data-hasfiles="1"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-chat-messages', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-chat-messages"
                    onclick="tryItOut('POSTapi-chat-messages');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-chat-messages"
                    onclick="cancelTryOut('POSTapi-chat-messages');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-chat-messages"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/chat/messages</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-chat-messages"
               value="multipart/form-data"
               data-component="header">
    <br>
<p>Example: <code>multipart/form-data</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-chat-messages"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>body</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="body"                data-endpoint="POSTapi-chat-messages"
               value="Good morning team!"
               data-component="body">
    <br>
<p>optional Message text (max 5000 chars). Example: <code>Good morning team!</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>link_url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="link_url"                data-endpoint="POSTapi-chat-messages"
               value="https://example.com"
               data-component="body">
    <br>
<p>optional URL to share. Example: <code>https://example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>attachment</code></b>&nbsp;&nbsp;
<small>file</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="file" style="display: none"
                              name="attachment"                data-endpoint="POSTapi-chat-messages"
               value=""
               data-component="body">
    <br>
<p>optional File attachment (jpg, png, pdf, doc, xls, zip — max 10MB). Example: <code>C:\Users\VJ Kyle\AppData\Local\Temp\php851B.tmp</code></p>
        </div>
        </form>

                    <h2 id="live-team-chat-POSTapi-chat-heartbeat">Chat presence heartbeat</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Keeps the current user marked as online in team chat.</p>

<span id="example-requests-POSTapi-chat-heartbeat">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/chat/heartbeat" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/chat/heartbeat"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-chat-heartbeat">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;online_users&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Admin&quot;,
            &quot;initials&quot;: &quot;A&quot;
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-chat-heartbeat" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-chat-heartbeat"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-chat-heartbeat"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-chat-heartbeat" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-chat-heartbeat">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-chat-heartbeat" data-method="POST"
      data-path="api/chat/heartbeat"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-chat-heartbeat', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-chat-heartbeat"
                    onclick="tryItOut('POSTapi-chat-heartbeat');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-chat-heartbeat"
                    onclick="cancelTryOut('POSTapi-chat-heartbeat');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-chat-heartbeat"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/chat/heartbeat</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-chat-heartbeat"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-chat-heartbeat"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="expenses">Expenses</h1>

    

                                <h2 id="expenses-GETapi-expenses-vehicles-search">Search vehicles for expense autocomplete</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-expenses-vehicles-search">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/expenses/vehicles/search?q=ABC" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/expenses/vehicles/search"
);

const params = {
    "q": "ABC",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-expenses-vehicles-search">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">[
    {
        &quot;id&quot;: 1,
        &quot;plate_number&quot;: &quot;ABC 1234&quot;,
        &quot;make&quot;: &quot;Toyota&quot;,
        &quot;model&quot;: &quot;Vios&quot;,
        &quot;year&quot;: 2020,
        &quot;full_name&quot;: &quot;2020 Toyota Vios&quot;
    }
]</code>
 </pre>
    </span>
<span id="execution-results-GETapi-expenses-vehicles-search" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-expenses-vehicles-search"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-expenses-vehicles-search"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-expenses-vehicles-search" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-expenses-vehicles-search">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-expenses-vehicles-search" data-method="GET"
      data-path="api/expenses/vehicles/search"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-expenses-vehicles-search', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-expenses-vehicles-search"
                    onclick="tryItOut('GETapi-expenses-vehicles-search');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-expenses-vehicles-search"
                    onclick="cancelTryOut('GETapi-expenses-vehicles-search');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-expenses-vehicles-search"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/expenses/vehicles/search</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-expenses-vehicles-search"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-expenses-vehicles-search"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>q</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="q"                data-endpoint="GETapi-expenses-vehicles-search"
               value="ABC"
               data-component="query">
    <br>
<p>optional Search plate, make, or model. Example: <code>ABC</code></p>
            </div>
                </form>

                <h1 id="other-endpoints">Other Endpoints</h1>

    

                                <h2 id="other-endpoints-GETapi-user">GET api/user</h2>

<p>
</p>



<span id="example-requests-GETapi-user">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/user" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/user"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-user">
            <blockquote>
            <p>Example response (302):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
location: http://localhost/login
content-type: text/html; charset=utf-8
x-ratelimit-limit: 60
x-ratelimit-remaining: 59
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;meta charset=&quot;UTF-8&quot; /&gt;
        &lt;meta http-equiv=&quot;refresh&quot; content=&quot;0;url=&#039;http://localhost/login&#039;&quot; /&gt;

        &lt;title&gt;Redirecting to http://localhost/login&lt;/title&gt;
    &lt;/head&gt;
    &lt;body&gt;
        Redirecting to &lt;a href=&quot;http://localhost/login&quot;&gt;http://localhost/login&lt;/a&gt;.
    &lt;/body&gt;
&lt;/html&gt;</code>
 </pre>
    </span>
<span id="execution-results-GETapi-user" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-user"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-user"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-user" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-user">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-user" data-method="GET"
      data-path="api/user"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-user', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-user"
                    onclick="tryItOut('GETapi-user');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-user"
                    onclick="cancelTryOut('GETapi-user');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-user"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/user</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="other-endpoints-GETapi-makes-search">GET api/makes/search</h2>

<p>
</p>



<span id="example-requests-GETapi-makes-search">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/makes/search" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/makes/search"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-makes-search">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6IlllbVhqcE1wNlZRR09mZ0V2WFdLb3c9PSIsInZhbHVlIjoibXdEdExXdkw2MGloc21BUWRSSGMrWjRkUnpuRHFVeTlEZXByaFNhQXZnRWRwOGJ6UFF6R2RKQmxLOTVRLzRONm5zRHowazBVSElBcHhBYnBocEt0YjlWaTFyWVEwY25laEhNTVM0ZUdqcXpjT056TjJkNmh4U3Jjc0l2R2hRZTUiLCJtYWMiOiIwYmM1MDZhZjQ4YzRmYmI2ODQ1MzY2MjNlMmY2NWQ0M2I5MWVlMjkxY2Q5MTBjNzJkM2E3NjkyNTI5ZDBmYjcyIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:36 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6Ilk5d2lUOWE2ZHRjazMvUjg4ZFlwZ3c9PSIsInZhbHVlIjoiSVBKSFl6c1NmbFcwU0pSMVRrQXdxd01nWHdsR0U3RFpWaUJSeFM5U2doTVZmRE84ekF5d1FlaHFEdE5aSnljdUN3QWZuSUFiS1pzRFF0dmV6ZmU1OUZSNUY1c2V2OHF6RXZLSTd3Y2RVZDUrdnM2VWdCN2x1Z011ZHlTSFQzSDUiLCJtYWMiOiJkZjMwNjM3OGM4MTU5NjNiM2M0YjllYzBjMWI4YTA4YjNkNjdjYzU2ZjhhZDk3NmQyMTdjNzQ3Y2VkODI2ZDU4IiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:36 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">[]</code>
 </pre>
    </span>
<span id="execution-results-GETapi-makes-search" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-makes-search"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-makes-search"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-makes-search" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-makes-search">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-makes-search" data-method="GET"
      data-path="api/makes/search"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-makes-search', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-makes-search"
                    onclick="tryItOut('GETapi-makes-search');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-makes-search"
                    onclick="cancelTryOut('GETapi-makes-search');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-makes-search"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/makes/search</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-makes-search"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-makes-search"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="other-endpoints-GETapi-models-search">GET api/models/search</h2>

<p>
</p>



<span id="example-requests-GETapi-models-search">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/models/search" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/models/search"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-models-search">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6IjlHTFF0OEFmekVrLy80UXcvTmhJdHc9PSIsInZhbHVlIjoiWk1iMmJEMXNvdGhyTFc2ams1c0NqMU83N1lTTVgzTWJIT2VjUHVVZ1JXNTRaYzJHTDBzYW9FRmVITG5HS2k2Kzk1OFA4ZjlkQWlJQkRpNk1vRmsrTWViTld0eHVOOTZUSVRRYU1ibEs5NW5KZjRiR0ZXMHZwVjFBN1h4cjZDdGQiLCJtYWMiOiI1NDA2ZjRjYmM4MGUyZWFlYzc1ZDVlYmIwYTJjYzIxYWNiMjgwYzI4NGEyNGQ3ZjgzOTI0MTQ1MjU5MTc2MjYzIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:36 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IlhrVndjRzlUdXkvLzFRSnhIMXRiQ0E9PSIsInZhbHVlIjoiN1dZMTJzL2JQMjR1WDMwL2c4NEg4M3ZYV0E1eFBKTlc4REVPMmg2cDF0UWpkQWJReENHTFM0MkdVSk5jS1VsOHhqa0cxU2lnNTdXajFjZi9RNGpGcTRTV3NtaitjMjIxdVFFNVczbk8rdEY1WFJSTmhXeStxMzcwd0tJOEoyVXkiLCJtYWMiOiI2MWNkN2E4ZDc5ODc3NjRiMWU2NDRiZjE1OTJlZDBhY2RkOWY3MTBiODBiYWVhNTI1OWI3OGE5NzZmNDZlZmY1IiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:36 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">[]</code>
 </pre>
    </span>
<span id="execution-results-GETapi-models-search" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-models-search"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-models-search"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-models-search" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-models-search">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-models-search" data-method="GET"
      data-path="api/models/search"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-models-search', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-models-search"
                    onclick="tryItOut('GETapi-models-search');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-models-search"
                    onclick="cancelTryOut('GETapi-models-search');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-models-search"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/models/search</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-models-search"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-models-search"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="other-endpoints-GETapi-contracts-vehicles-search">Search vehicles for contract (autocomplete).</h2>

<p>
</p>



<span id="example-requests-GETapi-contracts-vehicles-search">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/contracts/vehicles/search" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/contracts/vehicles/search"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-contracts-vehicles-search">
            <blockquote>
            <p>Example response (302):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
location: http://localhost/login
content-type: text/html; charset=utf-8
set-cookie: XSRF-TOKEN=eyJpdiI6Ik1nUkd1Z2xEWFM5ODBrdDVubmthTkE9PSIsInZhbHVlIjoiNC9yclZCaW13WEFQVnNRcHQvY2hTcjR1SkpNWk50VFFqcjRCSGk1V1BBOWxCU1g2WEJnaVN4cEozN3pNeGxTLzhKRVU4TmRTZFo3WHFZWlFUQ3F6NUo4a2orZ2hVS0dnSVBrNW9QWmo1UmlWTW1PMlFSbnVhUnpZbExJeGovRisiLCJtYWMiOiI5YjVhZDcwYzMwOGVhNjE2ODc3NTc2MzVlYjA2NmQxZjg5YTg1NDM1YzAxMDdiODM2YTJmZTA5MTFiYWI4ZDA1IiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:36 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IlVtdGcvUW1ZV0JhS09jQjV5dHhhRkE9PSIsInZhbHVlIjoiM1RTOTVvVHZwMXgwWjRFNm05VUQvMUY4WnlxajVvNHc0RXF5V1phNTRhRFVDdzdrMzIxT3Rua1N3aDZkc2hZc3FvNGRSL3dIRmxPUUxUOVVSSFBhQUpGOFM3VDhsYVljKzVUbmVZaHFMTzdKMUNFVjNEbTB2UUlKT1ZqeTFZMC8iLCJtYWMiOiI0NTRlYTA3MzE2YzUwMDc5OGNkYWY0Zjg4ZmFiYmY2OGQ0NjhhNGUxNTQ2ZDcxNmEzMjRkY2UwMjU1ZTJkODc4IiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:36 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;meta charset=&quot;UTF-8&quot; /&gt;
        &lt;meta http-equiv=&quot;refresh&quot; content=&quot;0;url=&#039;http://localhost/login&#039;&quot; /&gt;

        &lt;title&gt;Redirecting to http://localhost/login&lt;/title&gt;
    &lt;/head&gt;
    &lt;body&gt;
        Redirecting to &lt;a href=&quot;http://localhost/login&quot;&gt;http://localhost/login&lt;/a&gt;.
    &lt;/body&gt;
&lt;/html&gt;</code>
 </pre>
    </span>
<span id="execution-results-GETapi-contracts-vehicles-search" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-contracts-vehicles-search"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-contracts-vehicles-search"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-contracts-vehicles-search" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-contracts-vehicles-search">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-contracts-vehicles-search" data-method="GET"
      data-path="api/contracts/vehicles/search"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-contracts-vehicles-search', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-contracts-vehicles-search"
                    onclick="tryItOut('GETapi-contracts-vehicles-search');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-contracts-vehicles-search"
                    onclick="cancelTryOut('GETapi-contracts-vehicles-search');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-contracts-vehicles-search"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/contracts/vehicles/search</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-contracts-vehicles-search"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-contracts-vehicles-search"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="other-endpoints-GETapi-sales-agent-commissions-agents-search">JSON typeahead for commission forms: match name or staff ID code.</h2>

<p>
</p>



<span id="example-requests-GETapi-sales-agent-commissions-agents-search">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/sales-agent-commissions/agents/search" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/sales-agent-commissions/agents/search"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-sales-agent-commissions-agents-search">
            <blockquote>
            <p>Example response (302):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
location: http://localhost/login
content-type: text/html; charset=utf-8
set-cookie: XSRF-TOKEN=eyJpdiI6ImRMUWwzU1I5cjBIRWJQUUgvV25nc1E9PSIsInZhbHVlIjoiMElIa2J5VTIyS3Rpa09oVjJwTjI5MjJhNDNxOG1ZUTFRY1RCNEVlOXZES0lxemZRaEpOMGd5bTJrU3FMQ251ZkpVYWFKNytiY1VwLzkxMzJUdHp2NG13ZVE2Ym9YWW10VVdpa0FmSGVXaUQvZk82bkp1d1RiTzFtME9xTUd5WlYiLCJtYWMiOiJkMjM0NTUxMDU3ZWE1Y2YzZWY2MzMxYjNjOWQ4Mjk1YTc1OWQzYjllZjE4OWQyZGI5ODc4NGNhZWQ4NzRjYTc3IiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:36 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6InlqbEI5UmhqTGpac2FxUXd6Q3hIUkE9PSIsInZhbHVlIjoiUG1JbkFsd2VmSmlPdzlhbkJKcjI2NFdQNk1xR05SNGY0SDdySkd1QWt2OHVMaGNPWVFkbDNqTk55RVBNZUxmRDg3VU9yN1VJa1RRbWUvcEloTEZQTThrdENpMHBDZHArU1JRd1N5cVQ1czE1SEpOZ21oUERhYTRySWVMUCtmejQiLCJtYWMiOiJiZmRjNGIzYWNmYzE4YjNmNzk5MDAyYTc2MTYzYjlhM2E5NTI5MzExZTZjMmMxY2Y3NThiYTEzYWM4NGFmY2FjIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:36 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;meta charset=&quot;UTF-8&quot; /&gt;
        &lt;meta http-equiv=&quot;refresh&quot; content=&quot;0;url=&#039;http://localhost/login&#039;&quot; /&gt;

        &lt;title&gt;Redirecting to http://localhost/login&lt;/title&gt;
    &lt;/head&gt;
    &lt;body&gt;
        Redirecting to &lt;a href=&quot;http://localhost/login&quot;&gt;http://localhost/login&lt;/a&gt;.
    &lt;/body&gt;
&lt;/html&gt;</code>
 </pre>
    </span>
<span id="execution-results-GETapi-sales-agent-commissions-agents-search" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-sales-agent-commissions-agents-search"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-sales-agent-commissions-agents-search"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-sales-agent-commissions-agents-search" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-sales-agent-commissions-agents-search">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-sales-agent-commissions-agents-search" data-method="GET"
      data-path="api/sales-agent-commissions/agents/search"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-sales-agent-commissions-agents-search', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-sales-agent-commissions-agents-search"
                    onclick="tryItOut('GETapi-sales-agent-commissions-agents-search');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-sales-agent-commissions-agents-search"
                    onclick="cancelTryOut('GETapi-sales-agent-commissions-agents-search');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-sales-agent-commissions-agents-search"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/sales-agent-commissions/agents/search</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-sales-agent-commissions-agents-search"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-sales-agent-commissions-agents-search"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="other-endpoints-GETapi-expenses-vehicle-categories">Get all vehicle expense categories.</h2>

<p>
</p>



<span id="example-requests-GETapi-expenses-vehicle-categories">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/expenses/vehicle-categories" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/expenses/vehicle-categories"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-expenses-vehicle-categories">
            <blockquote>
            <p>Example response (302):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
location: http://localhost/login
content-type: text/html; charset=utf-8
set-cookie: XSRF-TOKEN=eyJpdiI6IkRnbFI0RWpvdFRVREVoclJkRWlwcXc9PSIsInZhbHVlIjoiVG9DRnp1QU9Oc2dLOWdWZ0pMclgxb0s0UGtUdFdiVys2a3MxVlUwODViMG9DYWtaNUZ6enJQQVhPODM2NnptcmV1RVJKcnNkRlZmSDVPdWFMbnJSajFqRmNIV0Q5cUJrenZyclNmdGtkM0RMcExpN2F3RU43em9UOHdnTXhzdWwiLCJtYWMiOiJiMGM3MWZhZjFlODczZmIzOTcxNmQ4Mzg4NThlMjAwNGVlM2FjZmQzZjZiODgzZDU0MDVjZjExOGQzMTliZjMyIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:36 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6ImYybDEwQWJEVnVJTTJucStRQ1JCZ2c9PSIsInZhbHVlIjoiR0h0SHNNQklBZlJqcWRkSGIvY0piN0ZZNWZDVTVRVWFoSXc2V3JYWXpHZnpsMFJnTVlkeTFzRFo3Qk9uMFpOczBiaXcyZFUrUFVHZ3VFdkNnSGFkQWpCUmxRUCs0YkVlbTkzTlpaS2dpaWNHOFJ3VUs5bFVhM2lOQ3ZjbXo5MEEiLCJtYWMiOiJmZjE1ODkzMDdiNjBkZjUzYzc4ZTEyODM0ZDM1NDFjZDM5NWM4MjdkMTliOGY3MTRkNzY2NWZmN2UxNGNlY2QzIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:36 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;meta charset=&quot;UTF-8&quot; /&gt;
        &lt;meta http-equiv=&quot;refresh&quot; content=&quot;0;url=&#039;http://localhost/login&#039;&quot; /&gt;

        &lt;title&gt;Redirecting to http://localhost/login&lt;/title&gt;
    &lt;/head&gt;
    &lt;body&gt;
        Redirecting to &lt;a href=&quot;http://localhost/login&quot;&gt;http://localhost/login&lt;/a&gt;.
    &lt;/body&gt;
&lt;/html&gt;</code>
 </pre>
    </span>
<span id="execution-results-GETapi-expenses-vehicle-categories" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-expenses-vehicle-categories"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-expenses-vehicle-categories"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-expenses-vehicle-categories" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-expenses-vehicle-categories">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-expenses-vehicle-categories" data-method="GET"
      data-path="api/expenses/vehicle-categories"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-expenses-vehicle-categories', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-expenses-vehicle-categories"
                    onclick="tryItOut('GETapi-expenses-vehicle-categories');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-expenses-vehicle-categories"
                    onclick="cancelTryOut('GETapi-expenses-vehicle-categories');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-expenses-vehicle-categories"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/expenses/vehicle-categories</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-expenses-vehicle-categories"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-expenses-vehicle-categories"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="other-endpoints-POSTapi-expenses-vehicle-categories">Add a new vehicle expense category.</h2>

<p>
</p>



<span id="example-requests-POSTapi-expenses-vehicle-categories">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/expenses/vehicle-categories" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/expenses/vehicle-categories"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-expenses-vehicle-categories">
</span>
<span id="execution-results-POSTapi-expenses-vehicle-categories" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-expenses-vehicle-categories"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-expenses-vehicle-categories"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-expenses-vehicle-categories" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-expenses-vehicle-categories">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-expenses-vehicle-categories" data-method="POST"
      data-path="api/expenses/vehicle-categories"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-expenses-vehicle-categories', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-expenses-vehicle-categories"
                    onclick="tryItOut('POSTapi-expenses-vehicle-categories');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-expenses-vehicle-categories"
                    onclick="cancelTryOut('POSTapi-expenses-vehicle-categories');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-expenses-vehicle-categories"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/expenses/vehicle-categories</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-expenses-vehicle-categories"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-expenses-vehicle-categories"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="other-endpoints-GETapi-expenses-payment-methods">Get all active payment methods.</h2>

<p>
</p>



<span id="example-requests-GETapi-expenses-payment-methods">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/expenses/payment-methods" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/expenses/payment-methods"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-expenses-payment-methods">
            <blockquote>
            <p>Example response (302):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
location: http://localhost/login
content-type: text/html; charset=utf-8
set-cookie: XSRF-TOKEN=eyJpdiI6IkZ6STl2cGVCU09HWURnVTVwS01Qb1E9PSIsInZhbHVlIjoiNzNsc0x6QWRMK1NqRTZxQmRYT1NNSWFBUWVNTXdqRGJMQnBCcmw4eGViWWd6dU91WDBZR1JqSGNnNCsxSlZvNnJmNUNkL0pzazZUTGFwekpPQ1QyNjBXVjU0VnE5andZaVZMYjl4TTBmMzdxTTY2Y0Z6WDVGL3BKUFNQU25JTlkiLCJtYWMiOiI4ZTQwODdjYjhlMWE4YzliYzRmZTJiNjc0OTM4ZGU3ZWI4MmU0NmJjYjE1YWMyM2IwMjMwNTVlY2FhNjRkMTQxIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:36 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6InFFQ21hdzNsUS84YzFOUExhcXozelE9PSIsInZhbHVlIjoiK240Q3NCdHpFdlZBNEJpWGlkSWN2NFBscUZpUkFBZWIvdG4yZGxsUitGVHFVVi9odVJ6dVhXL2xHQkVWY3JZd2wxZklSamRYWGF4TzhIYXVhZmNFQkx5YVRxQUQwQ2ZWc1U2Wk43U3Q2L0JXRUVjYWQ1QXVEM3RVTExsWS9qNlkiLCJtYWMiOiI4YmYyNTc2NjM2YmIyZTM1ZmE4MzYzN2NhMjRlYjJlMDI0ZDYzZGNjNGNmZWFhODE0MTljNWI3NTY5YjVjMzYzIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:36 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;meta charset=&quot;UTF-8&quot; /&gt;
        &lt;meta http-equiv=&quot;refresh&quot; content=&quot;0;url=&#039;http://localhost/login&#039;&quot; /&gt;

        &lt;title&gt;Redirecting to http://localhost/login&lt;/title&gt;
    &lt;/head&gt;
    &lt;body&gt;
        Redirecting to &lt;a href=&quot;http://localhost/login&quot;&gt;http://localhost/login&lt;/a&gt;.
    &lt;/body&gt;
&lt;/html&gt;</code>
 </pre>
    </span>
<span id="execution-results-GETapi-expenses-payment-methods" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-expenses-payment-methods"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-expenses-payment-methods"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-expenses-payment-methods" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-expenses-payment-methods">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-expenses-payment-methods" data-method="GET"
      data-path="api/expenses/payment-methods"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-expenses-payment-methods', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-expenses-payment-methods"
                    onclick="tryItOut('GETapi-expenses-payment-methods');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-expenses-payment-methods"
                    onclick="cancelTryOut('GETapi-expenses-payment-methods');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-expenses-payment-methods"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/expenses/payment-methods</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-expenses-payment-methods"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-expenses-payment-methods"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="other-endpoints-GETapi-tools-search">Search for tool names (autocomplete)</h2>

<p>
</p>



<span id="example-requests-GETapi-tools-search">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/tools/search" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/tools/search"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-tools-search">
            <blockquote>
            <p>Example response (302):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
location: http://localhost/login
content-type: text/html; charset=utf-8
set-cookie: XSRF-TOKEN=eyJpdiI6IldqVU50UGxrOXNSa0hIM2MwaDhrMnc9PSIsInZhbHVlIjoiZ2ZHSEk2MmgyWTcvdXdEUkdpd1RhcWd6NHhxVkRzRWk0bWpwanZxYlF4UFAxZ3Y4NjRScW1MaldGWTlPbVNMMjhQbDdVQUVWUU1PbldOZ1dTWGV4TVN0czRpV0FBaGdtVnJ0aHFXWmcybWxzRkhydW0vQXV5NEFXRUhONDA3SW0iLCJtYWMiOiI4M2JiNzFmMGQyYjY3ZjY2ODViZmI2OGE4ZDBhMjQ3OTEzMzcyYjc3ZjQ5MDI4MTMwNmQ2NGNjZjI0MmQzOGZmIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:36 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6Im1FM1BlREQzYnJ3aVJUMFF6M0dDb2c9PSIsInZhbHVlIjoieTA1UytaVjNPSnQ3ZmxHZytOd1ZrOS9wWHdFZXZ2WEl2dWoxcTYyNVl0UHZNSXA4ZWpjUVhPWTcrUm5ScjdzOFJJTnNvR214bkJ4ZjRyMWxaSk5vL1VyUlRzZGo0R3hhZWJsaURMVm5OUEM1cjJrVlZldEJ6MmdjU1E1YzUwTWUiLCJtYWMiOiIxMTUzNmY5NWM3ZmNiZjllZmUyYjIwZGQ5MzYwYTc3YWE1OTE0NWY3OTliODY3NzNhYWQ3NjhmYmZiZDM1MjU4IiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:36 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;meta charset=&quot;UTF-8&quot; /&gt;
        &lt;meta http-equiv=&quot;refresh&quot; content=&quot;0;url=&#039;http://localhost/login&#039;&quot; /&gt;

        &lt;title&gt;Redirecting to http://localhost/login&lt;/title&gt;
    &lt;/head&gt;
    &lt;body&gt;
        Redirecting to &lt;a href=&quot;http://localhost/login&quot;&gt;http://localhost/login&lt;/a&gt;.
    &lt;/body&gt;
&lt;/html&gt;</code>
 </pre>
    </span>
<span id="execution-results-GETapi-tools-search" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-tools-search"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-tools-search"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-tools-search" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-tools-search">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-tools-search" data-method="GET"
      data-path="api/tools/search"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-tools-search', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-tools-search"
                    onclick="tryItOut('GETapi-tools-search');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-tools-search"
                    onclick="cancelTryOut('GETapi-tools-search');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-tools-search"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/tools/search</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-tools-search"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-tools-search"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="other-endpoints-GETapi-tools-history">Get purchase history for a specific tool name</h2>

<p>
</p>



<span id="example-requests-GETapi-tools-history">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/tools/history" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/tools/history"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-tools-history">
            <blockquote>
            <p>Example response (302):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
location: http://localhost/login
content-type: text/html; charset=utf-8
set-cookie: XSRF-TOKEN=eyJpdiI6IjhzOGlqd3hpeGNRSzRiTTg0MVpuK3c9PSIsInZhbHVlIjoibG5PSHd6a0poU3NsNFdkekYxcXU5WEtBMWsyN0NubGRKajFEbUwrTUY0bGFRWFBDeERWWjNhczZBS3VzYjNLYThxSTc4ODlqQlI5L1ZINjZvMDlwOU0rcTlIYXNadXo3ektodFNCekl0Y3huWFFqN0x3V1pseTdrVm1FeUVIMTUiLCJtYWMiOiJkMWRkMDJlNTQxZjQxZTliN2JjNjVkZGM3ODRlODQ1MmE5Mzc2ZWNiNWFlNTYxZTEzZmYwZjdiNmFiN2JjNDJhIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:36 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IkFRZEtQU1h5dTEyeUtYVXRUQW4xWGc9PSIsInZhbHVlIjoieXRET3ZVd1VSQ1NDc1R6WkRwanRRaEwxUjJFVGNqVE9PcE5xRlBETzEwd0pxNlhSbkNvZHZZQTRBWDk3OEljVnQ2MnNxWTNCMTJ5M0Eyb0tWUVlSOGw1M3pwREgyL0hUSVlZQzcxczF2OFdmZ3Q2NmQyM2pZczhidGZoR3E4SVYiLCJtYWMiOiJlYjI0NmE3OTVmZmZkYTAwOWFjMzc1N2Q0MTViMjBkMzQzZGFlNjU5ZWFjMmQ2OTZkMjM1MDdiYzEyMDA3NDNmIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:36 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;meta charset=&quot;UTF-8&quot; /&gt;
        &lt;meta http-equiv=&quot;refresh&quot; content=&quot;0;url=&#039;http://localhost/login&#039;&quot; /&gt;

        &lt;title&gt;Redirecting to http://localhost/login&lt;/title&gt;
    &lt;/head&gt;
    &lt;body&gt;
        Redirecting to &lt;a href=&quot;http://localhost/login&quot;&gt;http://localhost/login&lt;/a&gt;.
    &lt;/body&gt;
&lt;/html&gt;</code>
 </pre>
    </span>
<span id="execution-results-GETapi-tools-history" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-tools-history"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-tools-history"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-tools-history" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-tools-history">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-tools-history" data-method="GET"
      data-path="api/tools/history"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-tools-history', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-tools-history"
                    onclick="tryItOut('GETapi-tools-history');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-tools-history"
                    onclick="cancelTryOut('GETapi-tools-history');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-tools-history"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/tools/history</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-tools-history"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-tools-history"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="other-endpoints-POSTapi-tools">Store a newly created tool</h2>

<p>
</p>



<span id="example-requests-POSTapi-tools">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/tools" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"vmqeopfuudtdsufvyvddq\",
    \"quantity\": 2,
    \"amount\": 45,
    \"date_acquired\": \"2026-07-21T02:28:36\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/tools"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "vmqeopfuudtdsufvyvddq",
    "quantity": 2,
    "amount": 45,
    "date_acquired": "2026-07-21T02:28:36"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-tools">
</span>
<span id="execution-results-POSTapi-tools" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-tools"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-tools"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-tools" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-tools">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-tools" data-method="POST"
      data-path="api/tools"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-tools', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-tools"
                    onclick="tryItOut('POSTapi-tools');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-tools"
                    onclick="cancelTryOut('POSTapi-tools');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-tools"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/tools</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-tools"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-tools"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-tools"
               value="vmqeopfuudtdsufvyvddq"
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>vmqeopfuudtdsufvyvddq</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>quantity</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="quantity"                data-endpoint="POSTapi-tools"
               value="2"
               data-component="body">
    <br>
<p>Must be at least 1. Example: <code>2</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>amount</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="amount"                data-endpoint="POSTapi-tools"
               value="45"
               data-component="body">
    <br>
<p>Must be at least 0. Example: <code>45</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>date_acquired</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date_acquired"                data-endpoint="POSTapi-tools"
               value="2026-07-21T02:28:36"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-07-21T02:28:36</code></p>
        </div>
        </form>

                    <h2 id="other-endpoints-GETapi-tools--id-">Display the specified tool</h2>

<p>
</p>



<span id="example-requests-GETapi-tools--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/tools/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/tools/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-tools--id-">
            <blockquote>
            <p>Example response (302):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
location: http://localhost/login
content-type: text/html; charset=utf-8
set-cookie: XSRF-TOKEN=eyJpdiI6IkpwSTg1TE5tdUdOenlsbWFDMUtjOGc9PSIsInZhbHVlIjoiMGQzYUxjd05BOVkvanVqL3RWUk45OEVmcEdSY3orQjFEOHB3WXhhMmhQTkF0TzlLNTc1UitjYVZSTVJWRWY1TURYL0Y2Tm5GWnM2eDVZb0dId1hWZURZekdjVTV5MWFlZHorVzZ1cG8yTmF1N21waS9Ea1ZFUG9lSHF5VHVrREsiLCJtYWMiOiJkMDI4YmRlYTVhOTQzZTM0OTAxNzk4ODg0ODliNWI1N2M5YWFhNjNiZGQzM2VhNjBmMGZkODFlZTNlZjNjYmFiIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:37 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IlNKZk1yZkFCNzlXTDczSStsc2JXRHc9PSIsInZhbHVlIjoiQVc5THM4SmE2MjhKZzJRMzc0T0g4eXJON2RmM3BhWHd4dFFtL3Vzb2pFS0xIb1hiK1c4UUMrQXZMbnRESDhsZ3JuR255WktwUW42YjV4N2xYbndyN0ljd1ZmV0UyQnkrb1NHN2tGWjZERVNtaGFmRlVnNnZ5WnZZZDN3c1E2dUciLCJtYWMiOiIyODQ1MGQwNmIyMTEyYjQzYjdkNTkzM2Y1MTE1ZmY5ZjJlNzljZDkyMjcxNGUwZDY1OWIwM2NlNjY1MTI0Y2Y0IiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:37 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;meta charset=&quot;UTF-8&quot; /&gt;
        &lt;meta http-equiv=&quot;refresh&quot; content=&quot;0;url=&#039;http://localhost/login&#039;&quot; /&gt;

        &lt;title&gt;Redirecting to http://localhost/login&lt;/title&gt;
    &lt;/head&gt;
    &lt;body&gt;
        Redirecting to &lt;a href=&quot;http://localhost/login&quot;&gt;http://localhost/login&lt;/a&gt;.
    &lt;/body&gt;
&lt;/html&gt;</code>
 </pre>
    </span>
<span id="execution-results-GETapi-tools--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-tools--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-tools--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-tools--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-tools--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-tools--id-" data-method="GET"
      data-path="api/tools/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-tools--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-tools--id-"
                    onclick="tryItOut('GETapi-tools--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-tools--id-"
                    onclick="cancelTryOut('GETapi-tools--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-tools--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/tools/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-tools--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-tools--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-tools--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the tool. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="other-endpoints-PUTapi-tools--id-">Update the specified tool</h2>

<p>
</p>



<span id="example-requests-PUTapi-tools--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/api/tools/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"vmqeopfuudtdsufvyvddq\",
    \"quantity\": 2,
    \"amount\": 45,
    \"date_acquired\": \"2026-07-21T02:28:37\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/tools/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "vmqeopfuudtdsufvyvddq",
    "quantity": 2,
    "amount": 45,
    "date_acquired": "2026-07-21T02:28:37"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-tools--id-">
</span>
<span id="execution-results-PUTapi-tools--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-tools--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-tools--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-tools--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-tools--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-tools--id-" data-method="PUT"
      data-path="api/tools/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-tools--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-tools--id-"
                    onclick="tryItOut('PUTapi-tools--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-tools--id-"
                    onclick="cancelTryOut('PUTapi-tools--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-tools--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/tools/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-tools--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-tools--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PUTapi-tools--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the tool. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-tools--id-"
               value="vmqeopfuudtdsufvyvddq"
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>vmqeopfuudtdsufvyvddq</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>quantity</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="quantity"                data-endpoint="PUTapi-tools--id-"
               value="2"
               data-component="body">
    <br>
<p>Must be at least 1. Example: <code>2</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>amount</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="amount"                data-endpoint="PUTapi-tools--id-"
               value="45"
               data-component="body">
    <br>
<p>Must be at least 0. Example: <code>45</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>date_acquired</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date_acquired"                data-endpoint="PUTapi-tools--id-"
               value="2026-07-21T02:28:37"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-07-21T02:28:37</code></p>
        </div>
        </form>

                    <h2 id="other-endpoints-DELETEapi-tools--id-">Remove the specified tool</h2>

<p>
</p>



<span id="example-requests-DELETEapi-tools--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost/api/tools/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/tools/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-tools--id-">
</span>
<span id="execution-results-DELETEapi-tools--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-tools--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-tools--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-tools--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-tools--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-tools--id-" data-method="DELETE"
      data-path="api/tools/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-tools--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-tools--id-"
                    onclick="tryItOut('DELETEapi-tools--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-tools--id-"
                    onclick="cancelTryOut('DELETEapi-tools--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-tools--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/tools/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-tools--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-tools--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-tools--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the tool. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="other-endpoints-GETapi-models--make-">GET api/models/{make}</h2>

<p>
</p>



<span id="example-requests-GETapi-models--make-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/models/consequatur" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/models/consequatur"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-models--make-">
            <blockquote>
            <p>Example response (302):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
location: http://localhost/login
content-type: text/html; charset=utf-8
set-cookie: XSRF-TOKEN=eyJpdiI6IkhLMXZyNDliWGV1VVZuTm56N3JnN0E9PSIsInZhbHVlIjoiempuSndtYXp4dzkvN0Z2WGhqeTE5S1B2TGhlZnJ2V2dQMEdEK1BMNWlWNUJVdzhZMHkybDFlaDZrVlYyK3ZuT0FQd3htYlpwQzh0eTZoRlVNZ1VoOW5iTk1IWXRzcGt4a3hoTEY1TmJmRGdCSE92M1c2WnhBay8wY3l2KzZ4MS8iLCJtYWMiOiJmMGVlMjEwNWM5MjhiYzg1MTBmZmY2ZmFhMGUwMDRmZWYyODMyYWVjZWNiMGQ4Njk4ZTFlZDBmMTNiMDQ3ZWQ0IiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:37 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IjhKaUIrVGdER3FuUXBNVnRhWkxDMVE9PSIsInZhbHVlIjoiVnVkZTRSUkYzSW0zY3VId0RxRDFXWUtiMnBjSFVVSGQ5RU14SllLMUNUVGkzRlpsZnRLSGFVN0dtMzNvOGp4dk42c0Z3a2hEbml0S2h0bXFXYnFGaS9MQmZlZXVjWTkxQnFVa3dLVkdiSHpmL0h4UEJZNGxZYnVFbkNqSU9DekEiLCJtYWMiOiJiM2RjMjBiNzY1M2UxNThlYzE1ZGMzM2EzMjZlMzVkZWE5NTE1OTQ1NWRiZjUzNzQ5ZGFmYmRkODdiNzQwMmJmIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:37 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;meta charset=&quot;UTF-8&quot; /&gt;
        &lt;meta http-equiv=&quot;refresh&quot; content=&quot;0;url=&#039;http://localhost/login&#039;&quot; /&gt;

        &lt;title&gt;Redirecting to http://localhost/login&lt;/title&gt;
    &lt;/head&gt;
    &lt;body&gt;
        Redirecting to &lt;a href=&quot;http://localhost/login&quot;&gt;http://localhost/login&lt;/a&gt;.
    &lt;/body&gt;
&lt;/html&gt;</code>
 </pre>
    </span>
<span id="execution-results-GETapi-models--make-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-models--make-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-models--make-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-models--make-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-models--make-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-models--make-" data-method="GET"
      data-path="api/models/{make}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-models--make-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-models--make-"
                    onclick="tryItOut('GETapi-models--make-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-models--make-"
                    onclick="cancelTryOut('GETapi-models--make-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-models--make-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/models/{make}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-models--make-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-models--make-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>make</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="make"                data-endpoint="GETapi-models--make-"
               value="consequatur"
               data-component="url">
    <br>
<p>Example: <code>consequatur</code></p>
            </div>
                    </form>

                    <h2 id="other-endpoints-GETapi-soa-transactions">Get transactions for a specific payment method and date.</h2>

<p>
</p>



<span id="example-requests-GETapi-soa-transactions">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/soa/transactions" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/soa/transactions"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-soa-transactions">
            <blockquote>
            <p>Example response (302):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
location: http://localhost/login
content-type: text/html; charset=utf-8
set-cookie: XSRF-TOKEN=eyJpdiI6IjlPcUlyM0RvNVlJRDBSUTFpWHYyZkE9PSIsInZhbHVlIjoiOXR4VjhWYjNITzdFVWNxMkNmK0JNTjROZUNxc3FkZmgyaFVsSXl0QlVVVldMOE1ZaW9RWTYyRVpxbU5vb0RlWnNSWEx6bzlONVRRZmd1dW9kSWNEL2xDbnFwcXhUNDJmZGsyL2xWL1hLZTJxczFoZVRkOExRTGNFbytmYyt5NFoiLCJtYWMiOiIxMDE2YTg1MWViM2YzNzdmZDk2YmIxZWQzNWYzMzZmNzE1ZjgyNmJiNWYwMDUwMzNlYjA1YWZlNjMzYTk0YzVjIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:37 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IkZWemhNR29jWExNaGlFZCt0WUFGN3c9PSIsInZhbHVlIjoiMkxGY2lhd0hPd2swRkUxekV1MmN0Rmw4S2NVRllXMysrdnN6VXZ0Tk1HTE0vUU44YS9UN2JTeHArcDk0dDNZZ1pHbjFQVjFqM2swaVBBUW5GZUJXbFhqV3ptZWx2WWRacGhJQ3UwYS9IK0JSOGhPK3d3MnpRT3Ewam9CK1lXS2MiLCJtYWMiOiIyMDA2NmVmNWNiYzdlOWVmZTFhOGExOGVmZTgzMTAyZjhiMDA0MzU1MTk2MDQ1M2VhYWUwZTRhMDlkNWNmYzRiIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:37 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;meta charset=&quot;UTF-8&quot; /&gt;
        &lt;meta http-equiv=&quot;refresh&quot; content=&quot;0;url=&#039;http://localhost/login&#039;&quot; /&gt;

        &lt;title&gt;Redirecting to http://localhost/login&lt;/title&gt;
    &lt;/head&gt;
    &lt;body&gt;
        Redirecting to &lt;a href=&quot;http://localhost/login&quot;&gt;http://localhost/login&lt;/a&gt;.
    &lt;/body&gt;
&lt;/html&gt;</code>
 </pre>
    </span>
<span id="execution-results-GETapi-soa-transactions" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-soa-transactions"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-soa-transactions"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-soa-transactions" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-soa-transactions">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-soa-transactions" data-method="GET"
      data-path="api/soa/transactions"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-soa-transactions', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-soa-transactions"
                    onclick="tryItOut('GETapi-soa-transactions');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-soa-transactions"
                    onclick="cancelTryOut('GETapi-soa-transactions');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-soa-transactions"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/soa/transactions</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-soa-transactions"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-soa-transactions"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="other-endpoints-POSTapi-soa-daily-budget">Store or update daily budget.</h2>

<p>
</p>



<span id="example-requests-POSTapi-soa-daily-budget">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/soa/daily-budget" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"payment_method_id\": \"consequatur\",
    \"budget_date\": \"2026-07-21T02:28:37\",
    \"starting_balance\": 45,
    \"added_cash\": 56,
    \"notes\": \"eopfuudtdsufvyvddqamn\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/soa/daily-budget"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "payment_method_id": "consequatur",
    "budget_date": "2026-07-21T02:28:37",
    "starting_balance": 45,
    "added_cash": 56,
    "notes": "eopfuudtdsufvyvddqamn"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-soa-daily-budget">
</span>
<span id="execution-results-POSTapi-soa-daily-budget" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-soa-daily-budget"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-soa-daily-budget"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-soa-daily-budget" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-soa-daily-budget">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-soa-daily-budget" data-method="POST"
      data-path="api/soa/daily-budget"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-soa-daily-budget', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-soa-daily-budget"
                    onclick="tryItOut('POSTapi-soa-daily-budget');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-soa-daily-budget"
                    onclick="cancelTryOut('POSTapi-soa-daily-budget');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-soa-daily-budget"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/soa/daily-budget</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-soa-daily-budget"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-soa-daily-budget"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>payment_method_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="payment_method_id"                data-endpoint="POSTapi-soa-daily-budget"
               value="consequatur"
               data-component="body">
    <br>
<p>Must match an existing stored value. Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>budget_date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="budget_date"                data-endpoint="POSTapi-soa-daily-budget"
               value="2026-07-21T02:28:37"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-07-21T02:28:37</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>starting_balance</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="starting_balance"                data-endpoint="POSTapi-soa-daily-budget"
               value="45"
               data-component="body">
    <br>
<p>Must be at least 0. Example: <code>45</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>added_cash</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="added_cash"                data-endpoint="POSTapi-soa-daily-budget"
               value="56"
               data-component="body">
    <br>
<p>Must be at least 0. Example: <code>56</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>notes</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="notes"                data-endpoint="POSTapi-soa-daily-budget"
               value="eopfuudtdsufvyvddqamn"
               data-component="body">
    <br>
<p>Must not be greater than 1000 characters. Example: <code>eopfuudtdsufvyvddqamn</code></p>
        </div>
        </form>

                    <h2 id="other-endpoints-POSTapi-soa-add-cash">Add cash (credit) to a payment method for a specific date.</h2>

<p>
</p>



<span id="example-requests-POSTapi-soa-add-cash">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/soa/add-cash" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"payment_method_id\": \"consequatur\",
    \"addition_date\": \"2026-07-21T02:28:37\",
    \"amount\": 45,
    \"description\": \"Amet iste laborum eius est dolor dolores.\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/soa/add-cash"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "payment_method_id": "consequatur",
    "addition_date": "2026-07-21T02:28:37",
    "amount": 45,
    "description": "Amet iste laborum eius est dolor dolores."
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-soa-add-cash">
</span>
<span id="execution-results-POSTapi-soa-add-cash" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-soa-add-cash"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-soa-add-cash"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-soa-add-cash" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-soa-add-cash">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-soa-add-cash" data-method="POST"
      data-path="api/soa/add-cash"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-soa-add-cash', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-soa-add-cash"
                    onclick="tryItOut('POSTapi-soa-add-cash');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-soa-add-cash"
                    onclick="cancelTryOut('POSTapi-soa-add-cash');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-soa-add-cash"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/soa/add-cash</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-soa-add-cash"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-soa-add-cash"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>payment_method_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="payment_method_id"                data-endpoint="POSTapi-soa-add-cash"
               value="consequatur"
               data-component="body">
    <br>
<p>Must match an existing stored value. Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>addition_date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="addition_date"                data-endpoint="POSTapi-soa-add-cash"
               value="2026-07-21T02:28:37"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-07-21T02:28:37</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>amount</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="amount"                data-endpoint="POSTapi-soa-add-cash"
               value="45"
               data-component="body">
    <br>
<p>Must be at least 0.01. Example: <code>45</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="description"                data-endpoint="POSTapi-soa-add-cash"
               value="Amet iste laborum eius est dolor dolores."
               data-component="body">
    <br>
<p>Must not be greater than 1000 characters. Example: <code>Amet iste laborum eius est dolor dolores.</code></p>
        </div>
        </form>

                    <h2 id="other-endpoints-GETapi-soa-cash-additions">Get all cash additions for a payment method and date.</h2>

<p>
</p>



<span id="example-requests-GETapi-soa-cash-additions">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/soa/cash-additions" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/soa/cash-additions"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-soa-cash-additions">
            <blockquote>
            <p>Example response (302):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
location: http://localhost/login
content-type: text/html; charset=utf-8
set-cookie: XSRF-TOKEN=eyJpdiI6ImNTZ01Bd25rK1ljVExna1IwYm12Q2c9PSIsInZhbHVlIjoiaEREQ29vTmoyM0hoWjhDY2V2ditRSzF1UCtsK1hQajlCd3dvaG9wUFVUdnZVRjdZSDFRRXFMYjZrVFFBYlBXc1BrUGdISU1BMVRSbEVCK0ZHQk9BR3RJM1NCcU1IZHVqS28rckI5bUkrQTVyY1lHZFJGa2pDV0JCbnNkTVpXbzgiLCJtYWMiOiJlZDg5YmMyMzA0ZjcwY2U2ZWZiY2M5YjgzMzkyYTdjN2NmMGZjN2MxOWZlZTUxOGQxYTVmZjljMGViMjI3MjIyIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:37 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6Ik5WZXc2M1hqUi9wVk5Qck9LV1luc3c9PSIsInZhbHVlIjoiSVNjWXFGSDZqUWJ0eU9OSDJic0RxNUpvMlF4bGRaWDhVUlBVVnRaMTZWejJLZE5zVW5KenU1bHRJR01wcGt3c0ZwYnh2MHdEbnBaQWNTZEJzeHFrbVNjSytwaHJxQXBZRkJDQVo3UjZmWVduVzVFcjdHVk1uTUwyTzBKZ1dycTAiLCJtYWMiOiJiZWZjNzIwMWJlN2ZmZDdmYTQzMzA4OWFlNDZlZTI1M2Y1MGQ4MGU3YmIzN2U5ZjhhMGVlZjUxODA1ZTZhZGEzIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:37 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;meta charset=&quot;UTF-8&quot; /&gt;
        &lt;meta http-equiv=&quot;refresh&quot; content=&quot;0;url=&#039;http://localhost/login&#039;&quot; /&gt;

        &lt;title&gt;Redirecting to http://localhost/login&lt;/title&gt;
    &lt;/head&gt;
    &lt;body&gt;
        Redirecting to &lt;a href=&quot;http://localhost/login&quot;&gt;http://localhost/login&lt;/a&gt;.
    &lt;/body&gt;
&lt;/html&gt;</code>
 </pre>
    </span>
<span id="execution-results-GETapi-soa-cash-additions" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-soa-cash-additions"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-soa-cash-additions"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-soa-cash-additions" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-soa-cash-additions">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-soa-cash-additions" data-method="GET"
      data-path="api/soa/cash-additions"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-soa-cash-additions', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-soa-cash-additions"
                    onclick="tryItOut('GETapi-soa-cash-additions');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-soa-cash-additions"
                    onclick="cancelTryOut('GETapi-soa-cash-additions');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-soa-cash-additions"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/soa/cash-additions</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-soa-cash-additions"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-soa-cash-additions"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="other-endpoints-PUTapi-soa-cash--id-">Update a cash addition (credit).</h2>

<p>
</p>



<span id="example-requests-PUTapi-soa-cash--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/api/soa/cash/consequatur" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"amount\": 73,
    \"description\": \"Dolorum amet iste laborum eius est dolor.\",
    \"addition_date\": \"2026-07-21T02:28:37\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/soa/cash/consequatur"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "amount": 73,
    "description": "Dolorum amet iste laborum eius est dolor.",
    "addition_date": "2026-07-21T02:28:37"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-soa-cash--id-">
</span>
<span id="execution-results-PUTapi-soa-cash--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-soa-cash--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-soa-cash--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-soa-cash--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-soa-cash--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-soa-cash--id-" data-method="PUT"
      data-path="api/soa/cash/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-soa-cash--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-soa-cash--id-"
                    onclick="tryItOut('PUTapi-soa-cash--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-soa-cash--id-"
                    onclick="cancelTryOut('PUTapi-soa-cash--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-soa-cash--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/soa/cash/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-soa-cash--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-soa-cash--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="PUTapi-soa-cash--id-"
               value="consequatur"
               data-component="url">
    <br>
<p>The ID of the cash. Example: <code>consequatur</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>amount</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="amount"                data-endpoint="PUTapi-soa-cash--id-"
               value="73"
               data-component="body">
    <br>
<p>Must be at least 0.01. Example: <code>73</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="description"                data-endpoint="PUTapi-soa-cash--id-"
               value="Dolorum amet iste laborum eius est dolor."
               data-component="body">
    <br>
<p>Must not be greater than 1000 characters. Example: <code>Dolorum amet iste laborum eius est dolor.</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>addition_date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="addition_date"                data-endpoint="PUTapi-soa-cash--id-"
               value="2026-07-21T02:28:37"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-07-21T02:28:37</code></p>
        </div>
        </form>

                    <h2 id="other-endpoints-DELETEapi-soa-cash--id-">Delete a cash addition (credit).</h2>

<p>
</p>



<span id="example-requests-DELETEapi-soa-cash--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost/api/soa/cash/consequatur" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/soa/cash/consequatur"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-soa-cash--id-">
</span>
<span id="execution-results-DELETEapi-soa-cash--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-soa-cash--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-soa-cash--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-soa-cash--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-soa-cash--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-soa-cash--id-" data-method="DELETE"
      data-path="api/soa/cash/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-soa-cash--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-soa-cash--id-"
                    onclick="tryItOut('DELETEapi-soa-cash--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-soa-cash--id-"
                    onclick="cancelTryOut('DELETEapi-soa-cash--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-soa-cash--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/soa/cash/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-soa-cash--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-soa-cash--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="DELETEapi-soa-cash--id-"
               value="consequatur"
               data-component="url">
    <br>
<p>The ID of the cash. Example: <code>consequatur</code></p>
            </div>
                    </form>

                    <h2 id="other-endpoints-PUTapi-soa-update-starting-cash">Update starting cash for a daily budget.</h2>

<p>
</p>



<span id="example-requests-PUTapi-soa-update-starting-cash">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/api/soa/update-starting-cash" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"payment_method_id\": \"consequatur\",
    \"budget_date\": \"2026-07-21T02:28:37\",
    \"starting_balance\": 45
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/soa/update-starting-cash"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "payment_method_id": "consequatur",
    "budget_date": "2026-07-21T02:28:37",
    "starting_balance": 45
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-soa-update-starting-cash">
</span>
<span id="execution-results-PUTapi-soa-update-starting-cash" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-soa-update-starting-cash"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-soa-update-starting-cash"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-soa-update-starting-cash" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-soa-update-starting-cash">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-soa-update-starting-cash" data-method="PUT"
      data-path="api/soa/update-starting-cash"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-soa-update-starting-cash', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-soa-update-starting-cash"
                    onclick="tryItOut('PUTapi-soa-update-starting-cash');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-soa-update-starting-cash"
                    onclick="cancelTryOut('PUTapi-soa-update-starting-cash');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-soa-update-starting-cash"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/soa/update-starting-cash</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-soa-update-starting-cash"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-soa-update-starting-cash"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>payment_method_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="payment_method_id"                data-endpoint="PUTapi-soa-update-starting-cash"
               value="consequatur"
               data-component="body">
    <br>
<p>Must match an existing stored value. Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>budget_date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="budget_date"                data-endpoint="PUTapi-soa-update-starting-cash"
               value="2026-07-21T02:28:37"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-07-21T02:28:37</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>starting_balance</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="starting_balance"                data-endpoint="PUTapi-soa-update-starting-cash"
               value="45"
               data-component="body">
    <br>
<p>Must be at least 0. Example: <code>45</code></p>
        </div>
        </form>

                    <h2 id="other-endpoints-POSTapi-soa-manual-entries">Store a manual SOA line (description + debit or credit) for a date.</h2>

<p>
</p>



<span id="example-requests-POSTapi-soa-manual-entries">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/soa/manual-entries" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"payment_method_id\": \"consequatur\",
    \"entry_date\": \"2026-07-21T02:28:37\",
    \"description\": \"Dolorum amet iste laborum eius est dolor.\",
    \"type\": \"credit\",
    \"amount\": 66,
    \"expense_budget\": false,
    \"expense_budget_tier\": \"warehouse\",
    \"is_carry_over\": true
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/soa/manual-entries"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "payment_method_id": "consequatur",
    "entry_date": "2026-07-21T02:28:37",
    "description": "Dolorum amet iste laborum eius est dolor.",
    "type": "credit",
    "amount": 66,
    "expense_budget": false,
    "expense_budget_tier": "warehouse",
    "is_carry_over": true
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-soa-manual-entries">
</span>
<span id="execution-results-POSTapi-soa-manual-entries" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-soa-manual-entries"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-soa-manual-entries"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-soa-manual-entries" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-soa-manual-entries">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-soa-manual-entries" data-method="POST"
      data-path="api/soa/manual-entries"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-soa-manual-entries', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-soa-manual-entries"
                    onclick="tryItOut('POSTapi-soa-manual-entries');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-soa-manual-entries"
                    onclick="cancelTryOut('POSTapi-soa-manual-entries');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-soa-manual-entries"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/soa/manual-entries</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-soa-manual-entries"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-soa-manual-entries"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>payment_method_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="payment_method_id"                data-endpoint="POSTapi-soa-manual-entries"
               value="consequatur"
               data-component="body">
    <br>
<p>Must match an existing stored value. Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>entry_date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="entry_date"                data-endpoint="POSTapi-soa-manual-entries"
               value="2026-07-21T02:28:37"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-07-21T02:28:37</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="description"                data-endpoint="POSTapi-soa-manual-entries"
               value="Dolorum amet iste laborum eius est dolor."
               data-component="body">
    <br>
<p>Must not be greater than 1000 characters. Example: <code>Dolorum amet iste laborum eius est dolor.</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="POSTapi-soa-manual-entries"
               value="credit"
               data-component="body">
    <br>
<p>Example: <code>credit</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>debit</code></li> <li><code>credit</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>amount</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="amount"                data-endpoint="POSTapi-soa-manual-entries"
               value="66"
               data-component="body">
    <br>
<p>Must be at least 0.01. Example: <code>66</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>expense_budget</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <label data-endpoint="POSTapi-soa-manual-entries" style="display: none">
            <input type="radio" name="expense_budget"
                   value="true"
                   data-endpoint="POSTapi-soa-manual-entries"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="POSTapi-soa-manual-entries" style="display: none">
            <input type="radio" name="expense_budget"
                   value="false"
                   data-endpoint="POSTapi-soa-manual-entries"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Example: <code>false</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>expense_budget_tier</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="expense_budget_tier"                data-endpoint="POSTapi-soa-manual-entries"
               value="warehouse"
               data-component="body">
    <br>
<p>Example: <code>warehouse</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>flagship</code></li> <li><code>warehouse</code></li> <li><code>annex</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>is_carry_over</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <label data-endpoint="POSTapi-soa-manual-entries" style="display: none">
            <input type="radio" name="is_carry_over"
                   value="true"
                   data-endpoint="POSTapi-soa-manual-entries"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="POSTapi-soa-manual-entries" style="display: none">
            <input type="radio" name="is_carry_over"
                   value="false"
                   data-endpoint="POSTapi-soa-manual-entries"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Example: <code>true</code></p>
        </div>
        </form>

                    <h2 id="other-endpoints-PUTapi-soa-manual-entries--soa_manual_entry_id-">Update a manual SOA line.</h2>

<p>
</p>



<span id="example-requests-PUTapi-soa-manual-entries--soa_manual_entry_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/api/soa/manual-entries/consequatur" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"description\": \"Dolores dolorum amet iste laborum eius est dolor.\",
    \"type\": \"credit\",
    \"amount\": 66,
    \"expense_budget\": false,
    \"expense_budget_tier\": \"warehouse\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/soa/manual-entries/consequatur"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "description": "Dolores dolorum amet iste laborum eius est dolor.",
    "type": "credit",
    "amount": 66,
    "expense_budget": false,
    "expense_budget_tier": "warehouse"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-soa-manual-entries--soa_manual_entry_id-">
</span>
<span id="execution-results-PUTapi-soa-manual-entries--soa_manual_entry_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-soa-manual-entries--soa_manual_entry_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-soa-manual-entries--soa_manual_entry_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-soa-manual-entries--soa_manual_entry_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-soa-manual-entries--soa_manual_entry_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-soa-manual-entries--soa_manual_entry_id-" data-method="PUT"
      data-path="api/soa/manual-entries/{soa_manual_entry_id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-soa-manual-entries--soa_manual_entry_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-soa-manual-entries--soa_manual_entry_id-"
                    onclick="tryItOut('PUTapi-soa-manual-entries--soa_manual_entry_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-soa-manual-entries--soa_manual_entry_id-"
                    onclick="cancelTryOut('PUTapi-soa-manual-entries--soa_manual_entry_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-soa-manual-entries--soa_manual_entry_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/soa/manual-entries/{soa_manual_entry_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-soa-manual-entries--soa_manual_entry_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-soa-manual-entries--soa_manual_entry_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>soa_manual_entry_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="soa_manual_entry_id"                data-endpoint="PUTapi-soa-manual-entries--soa_manual_entry_id-"
               value="consequatur"
               data-component="url">
    <br>
<p>The ID of the soa manual entry. Example: <code>consequatur</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="description"                data-endpoint="PUTapi-soa-manual-entries--soa_manual_entry_id-"
               value="Dolores dolorum amet iste laborum eius est dolor."
               data-component="body">
    <br>
<p>Must not be greater than 1000 characters. Example: <code>Dolores dolorum amet iste laborum eius est dolor.</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="PUTapi-soa-manual-entries--soa_manual_entry_id-"
               value="credit"
               data-component="body">
    <br>
<p>Example: <code>credit</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>debit</code></li> <li><code>credit</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>amount</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="amount"                data-endpoint="PUTapi-soa-manual-entries--soa_manual_entry_id-"
               value="66"
               data-component="body">
    <br>
<p>Must be at least 0.01. Example: <code>66</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>expense_budget</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <label data-endpoint="PUTapi-soa-manual-entries--soa_manual_entry_id-" style="display: none">
            <input type="radio" name="expense_budget"
                   value="true"
                   data-endpoint="PUTapi-soa-manual-entries--soa_manual_entry_id-"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="PUTapi-soa-manual-entries--soa_manual_entry_id-" style="display: none">
            <input type="radio" name="expense_budget"
                   value="false"
                   data-endpoint="PUTapi-soa-manual-entries--soa_manual_entry_id-"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Example: <code>false</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>expense_budget_tier</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="expense_budget_tier"                data-endpoint="PUTapi-soa-manual-entries--soa_manual_entry_id-"
               value="warehouse"
               data-component="body">
    <br>
<p>Example: <code>warehouse</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>flagship</code></li> <li><code>warehouse</code></li> <li><code>annex</code></li></ul>
        </div>
        </form>

                    <h2 id="other-endpoints-DELETEapi-soa-manual-entries--soa_manual_entry_id-">Remove a manual SOA line.</h2>

<p>
</p>



<span id="example-requests-DELETEapi-soa-manual-entries--soa_manual_entry_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost/api/soa/manual-entries/consequatur" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/soa/manual-entries/consequatur"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-soa-manual-entries--soa_manual_entry_id-">
</span>
<span id="execution-results-DELETEapi-soa-manual-entries--soa_manual_entry_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-soa-manual-entries--soa_manual_entry_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-soa-manual-entries--soa_manual_entry_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-soa-manual-entries--soa_manual_entry_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-soa-manual-entries--soa_manual_entry_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-soa-manual-entries--soa_manual_entry_id-" data-method="DELETE"
      data-path="api/soa/manual-entries/{soa_manual_entry_id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-soa-manual-entries--soa_manual_entry_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-soa-manual-entries--soa_manual_entry_id-"
                    onclick="tryItOut('DELETEapi-soa-manual-entries--soa_manual_entry_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-soa-manual-entries--soa_manual_entry_id-"
                    onclick="cancelTryOut('DELETEapi-soa-manual-entries--soa_manual_entry_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-soa-manual-entries--soa_manual_entry_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/soa/manual-entries/{soa_manual_entry_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-soa-manual-entries--soa_manual_entry_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-soa-manual-entries--soa_manual_entry_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>soa_manual_entry_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="soa_manual_entry_id"                data-endpoint="DELETEapi-soa-manual-entries--soa_manual_entry_id-"
               value="consequatur"
               data-component="url">
    <br>
<p>The ID of the soa manual entry. Example: <code>consequatur</code></p>
            </div>
                    </form>

                    <h2 id="other-endpoints-DELETEapi-soa-daily-record">Delete all SOA data for a payment method on a specific date.</h2>

<p>
</p>



<span id="example-requests-DELETEapi-soa-daily-record">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost/api/soa/daily-record" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"payment_method_id\": \"consequatur\",
    \"date\": \"2026-07-21T02:28:37\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/soa/daily-record"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "payment_method_id": "consequatur",
    "date": "2026-07-21T02:28:37"
};

fetch(url, {
    method: "DELETE",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-soa-daily-record">
</span>
<span id="execution-results-DELETEapi-soa-daily-record" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-soa-daily-record"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-soa-daily-record"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-soa-daily-record" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-soa-daily-record">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-soa-daily-record" data-method="DELETE"
      data-path="api/soa/daily-record"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-soa-daily-record', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-soa-daily-record"
                    onclick="tryItOut('DELETEapi-soa-daily-record');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-soa-daily-record"
                    onclick="cancelTryOut('DELETEapi-soa-daily-record');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-soa-daily-record"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/soa/daily-record</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-soa-daily-record"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-soa-daily-record"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>payment_method_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="payment_method_id"                data-endpoint="DELETEapi-soa-daily-record"
               value="consequatur"
               data-component="body">
    <br>
<p>Must match an existing stored value. Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date"                data-endpoint="DELETEapi-soa-daily-record"
               value="2026-07-21T02:28:37"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-07-21T02:28:37</code></p>
        </div>
        </form>

                    <h2 id="other-endpoints-GETapi-soa-floated-funds">Floated funds total and line items (declared starting below prior day&#039;s closing).</h2>

<p>
</p>



<span id="example-requests-GETapi-soa-floated-funds">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/soa/floated-funds" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/soa/floated-funds"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-soa-floated-funds">
            <blockquote>
            <p>Example response (302):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
location: http://localhost/login
content-type: text/html; charset=utf-8
set-cookie: XSRF-TOKEN=eyJpdiI6Im9zdDU2bWpNd0hTY1F2OG9ra1hnaHc9PSIsInZhbHVlIjoiNjBlSW1pZUNOZS93VkZFcWtRNjNxUnprZEVqVjdUQ2FER3FkK2xnY3hZT0J4TE8vZnZuTnhZYlY1aDV4c0hoMW5ZNURFb2FxcWc5OVNxa0c5c0VBTkFoWVltaHNDaVp3bnZTcWF1WlFJTEFYdWV2MWRDQjNBVWx1Y3RRb0d5emIiLCJtYWMiOiI5NzcyYzM2NGZhNjllMmExNTM2YjczMWY3ZmIxYTdiYmVhMzBjYjNkNTBlZmYzYmE1NzY5ZmJlYjc1NWM1YzczIiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:37 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6InJVR01jTU1wMk9VRXNvMGt0dGlpNWc9PSIsInZhbHVlIjoiMWZWQ3JTa0pKK1l3QzFCZ3k5cU1nVnlDUWNORVAwem4vM052ZzltQVNWdExSS2tkbmJpUWh3Z1hxQk8wWEFLN08rUGhSSFlaMitqRWdybHRaQUxhRElOcXBudW9odXR3SDN5MTloZHBsSDlwbkRYcWJlcWF6TTU0VURtU05NNjgiLCJtYWMiOiI4OTJjM2ZjNTllMjM2N2UzNGJmOTlmNWIwN2M5ZTIyY2M4OGFhNmZhZTUzNjc5N2I1NDZhNGI0MzhmMTIyZjI1IiwidGFnIjoiIn0%3D; expires=Tue, 21 Jul 2026 04:28:37 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;meta charset=&quot;UTF-8&quot; /&gt;
        &lt;meta http-equiv=&quot;refresh&quot; content=&quot;0;url=&#039;http://localhost/login&#039;&quot; /&gt;

        &lt;title&gt;Redirecting to http://localhost/login&lt;/title&gt;
    &lt;/head&gt;
    &lt;body&gt;
        Redirecting to &lt;a href=&quot;http://localhost/login&quot;&gt;http://localhost/login&lt;/a&gt;.
    &lt;/body&gt;
&lt;/html&gt;</code>
 </pre>
    </span>
<span id="execution-results-GETapi-soa-floated-funds" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-soa-floated-funds"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-soa-floated-funds"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-soa-floated-funds" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-soa-floated-funds">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-soa-floated-funds" data-method="GET"
      data-path="api/soa/floated-funds"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-soa-floated-funds', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-soa-floated-funds"
                    onclick="tryItOut('GETapi-soa-floated-funds');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-soa-floated-funds"
                    onclick="cancelTryOut('GETapi-soa-floated-funds');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-soa-floated-funds"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/soa/floated-funds</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-soa-floated-funds"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-soa-floated-funds"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
