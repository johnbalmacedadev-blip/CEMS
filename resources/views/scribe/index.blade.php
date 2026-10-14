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
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-POSTapi-mechanic-expense-records">
                                <a href="#other-endpoints-POSTapi-mechanic-expense-records">POST api/mechanic-expense-records</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-GETapi-mechanic-expense-records--mechanicExpenseRecord_id-">
                                <a href="#other-endpoints-GETapi-mechanic-expense-records--mechanicExpenseRecord_id-">GET api/mechanic-expense-records/{mechanicExpenseRecord_id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-">
                                <a href="#other-endpoints-PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-">PUT api/mechanic-expense-records/{mechanicExpenseRecord_id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="other-endpoints-DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-">
                                <a href="#other-endpoints-DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-">DELETE api/mechanic-expense-records/{mechanicExpenseRecord_id}</a>
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
        <li>Last updated: September 12, 2026</li>
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
    "http://localhost/vehicles/274/archive" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/vehicles/274/archive"
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
               value="274"
               data-component="url">
    <br>
<p>The ID of the vehicle. Example: <code>274</code></p>
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
    --form "attachment=@C:\Users\VJ Kyle\AppData\Local\Temp\php743.tmp" </code></pre></div>


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
<p>optional File attachment (jpg, png, pdf, doc, xls, zip — max 10MB). Example: <code>C:\Users\VJ Kyle\AppData\Local\Temp\php743.tmp</code></p>
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
set-cookie: XSRF-TOKEN=eyJpdiI6IjZQTDJnYlpFcTBnU2xwSmRYNCtzTGc9PSIsInZhbHVlIjoiTkRFMi9SMG9uaTNpQ1hNYzVWalgwOVFFdXBReVc0QnZEWFZvTVl5LzJwRnU5Y0hBbllPcXFMbjZpdk5ZZmVHWjJXZWdET2R0UTNFZ3lUMENMZEJJRStPUFJUd2V6bVU0cmU5d2hRdWZQYUsyU0x5RFNWNEtmeVlkMk51SFM4N2kiLCJtYWMiOiJkYTQ1OGRhNTU2YmMzMTQ3MjcxOGIxNzVlZDUyMDJjYmRkYTQ0YmYxMDA2NTg4OGMxZGE1ZGViZTE3YTFiMTgwIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:03 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6Ikw5MEtRQnNYVzJ6bzV6UkFLSzhPY0E9PSIsInZhbHVlIjoiMEZxeUFGM2hkRHRhWWpmZWdRTFpFalVHVXgvRFYrRzVFWlAzUDFOanB0Y3hvUEJ1eDN5K2o0WE92OTNpWThhaVBYNjhCVVJTMGFHb1d1Wk9PUVBWczNrZHlXNkQ4N3lLQ3lPS2RKMHV3Tm1FbURObVhra0d1aEZJT2pBdEFJVEIiLCJtYWMiOiJiMjEzOTBiNmIyMjA3NDhiNzQzOWVkZjY2ODRiYjE4YTFlNzJlYzU0MjQ3MmMwZGQyMDkwYTgxNTNhMDdiMGM4IiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:03 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6IjlpbHowamZxcHY5YWYzUVc2QWE2SHc9PSIsInZhbHVlIjoiZWN1NmNETUVnQ3ZuM3ZnaktFc3dCcWVTUE1EdW51cE1KZnRtek1mZUhEdFZoaGI4RzlmZnVFQndVMkxzSGVDTmJvcCtodksvaTM2UXczS09hckdRSEVkTnI0THZJTzVWWjRBUzRLWnhEU1k0eE5YS2NkSFVBL2RyYWtuUTN1SEEiLCJtYWMiOiI3YjdiNzI0N2Q3ZGJlNjBkNTJjZTdjOWM5MTY0YmJkYTBmZmIxODA3OTU5YzhkZjU0YjYzMmY3ZWJjYzM1NWE1IiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:03 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6Ims3Uk5QdUJQZDBOeDZSSXBBZEVsenc9PSIsInZhbHVlIjoiWWJBZEdnSVBvbFEzTGJNa0tycUpWR05rdlBrU3haanp1RHZrM2FXaG5SdmV0K29ySDNwV0ZyNzE0R0Q0TnpBYmMzYWFJU2JYL3pTd3VhSkxFWC83ZUtwWGIyMXlZV2lYQ2dVNlU2QzB1cHhmZGpNaUVoK1NDeWNGYy9rV3VTb2QiLCJtYWMiOiJhNjk2OTYxNmJhYTU4NjZmMzE2OWY2ZDY3OTBlY2MwOTU1YmE3OTg2Y2YyMTQyNjNjMzA4YWE2NGM1ZDU3OGM2IiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:03 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6IitacXpCVG8rc2RSc2xiVC9lSVhObFE9PSIsInZhbHVlIjoiQ0dyTFNiVG1JVnJuMW13TjFrcTlMTlRaU3dwNERNbE95UlJ6dlROZ1ZoeE5kWmJrUG4zakExTmVoY0IwOVVjVkl0NCt0UUh6QUgvbzR3YVFxZ3R2dmtxRWFBQWdNNUxHZkJualBOVjNBZCtpZllDQXlheTcrOEx2dDMxM3BndXAiLCJtYWMiOiI3NDVmNDQwNTY4YTJhY2EwYjBmNzFhZWRjZjMzNzQzMDA2ZjMyMDliYzc2NDkyNWVkMDRkOGVjOWQzZjZlMzQ2IiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:03 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IkRndGlLamJmOWM0WXIyZVlIbTh6dlE9PSIsInZhbHVlIjoiTjQ0aWxTbmFqb2FwNkR2RDRXaU1UWVdjTGRNSmMyZGlGRjllN0ZqclRUSHp6bWx0NjNTL0EvdCs3UG16Z0g1L1hTbkwvalFUMUxreEYydGdMcFlDclUyM2pkZTdBSys0ZExZR3EyRE12NzRQM2RvaFhhcVJxWlA4QldaNmhlRDgiLCJtYWMiOiIzNDI2YzEyZDI0OGVmOTRkODA3ZDhlYzMzMGUxNDQ2ODM3NTE3ZWQwZTM0MDBkNGI2ZTQzNGE0YTQyZDQzNjQxIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:03 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6IjE2L3JxdXR3RDFUWjdrYXVpWXdPdXc9PSIsInZhbHVlIjoiTDg1c1haZUtnVkdRaEtZR3p0Sll1cUtyMnhnOEhadHFGbFVGWGJnaGZYQnIwclVWZzR5Q1NOR1hML2xBdUc1cXdabXpybTJ3bXB6YzZmK08zUTYxSnpVaC95NGVNSkx3UC9BREpuYldjamIzY2l4S3JXbmMxQXZIL0h6aGtqVWkiLCJtYWMiOiIzZDg4MmM3MzA1ZmUxYjJmNDQ2ZjAxZjZmNjhmOTUyODU0M2NjMTg5M2I5N2EzN2QyNzQzMmU2M2Q0NzY4Y2QxIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:03 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IkJCTkF5NzRvQVVMK0NGRm83aG14aEE9PSIsInZhbHVlIjoic242Q0RqNGE0WTBUaW43SFJFd0ZaemxkV3I3QTZGeFhHdkpPbU05SU9aL1c0TzlaTmQ0RjhZWE1kNW9yM1d4NjYwSnRGZC9QTGtoa1dsb05NcGhUeWRCZjBXVDF1M0F0V00xWmZIWVVhc2hJcE9mK2pGemJBWlJrY2dTaFltQ3IiLCJtYWMiOiIxNmQ0OTFiZmNkNGI0NDI3NWM4MWUwZDY5N2UyZDI4NDRhMzZmODIxM2U3YmFlYjUxNmRlMjA1OTFhMGRiNGZmIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:03 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6ImJBbmNqYzgvR2hGMkF0T1k1bEVjSXc9PSIsInZhbHVlIjoiQ0xnMEs0VlYxV2E3WU5CY2lxMVl2dE54OFEzWmM3S3VoMjg3UFJLYVEwNXhaMDlnZ2NNeHg0NVA0WUZnZ3ZsT1dBb1VpZEsxcjc5UEt3S1dqR1p4T1dKdEF5anRyNWRzbnNrQ05JQmZuUjBGa1NwSTJsbFBoMkM2Mkg2UGhRUVMiLCJtYWMiOiI3NDBmMzk2NTNhOGQ0NzU3MmIzOGJjOTc5MGVjYzZiMWU5YzRiNzY5ZTcxODU5ZTFkMGQ2YWQwNjdlMzczYjUxIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:03 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6Ii9lM1FBbW83VEtsVm82MzJJZnR4NUE9PSIsInZhbHVlIjoiZnNvTU9mTC96T0RjRGl1am5zTWFUajdQTzN3MTZ3ZXZKV0xHZjVhVUpvL0lkK0pmdzFlcGw5d0xxY01VTjNwSVlMaFlTcWpwUHBuYSthUFlVT0xsU0lsRVExTm1uYW9WcTNYTjZacUlQbkJnMTlBZWZ6U0VrNGQyMHV4ZktNTWwiLCJtYWMiOiJlZmI5MzNhNzEzMGQ1OWUyNzBhZmFjZjEyYzkwOGM2MDhmZTcwZWVmYjRhYWJhOTE3MzY2ZmJlMjljOTgxN2RlIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:03 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6InRPaWxLaGY3Q1IyNDRoeGNzbGNIVUE9PSIsInZhbHVlIjoiMk1ZaDdtUXgxOGFlRFVZMjg0VTFNNmFDcXRPemdzSi8vM2pFTG9oWVJyMW9iNHVZZDFIOFJwQlU3NUVUc2xQZmc4b1Q0SXJPcmlCOFhIczNJUThLMVNhWkF4VFZVQTFVYnFnOWRCbkxqQVRrOEpGcW1XQ1RpZmVMN2FyOTBBaUoiLCJtYWMiOiJkMzQyMDQzZDg1NzhlMmQ2MzM0OWIwNjAwZDY5ZDNhMzNkYzg2NDIzNmM4YWY5ZDE1MTc4NDYwYmNmOWQ2YTFjIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:04 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6InVZOXFMeGlid01IMVRhTlJGWVljMmc9PSIsInZhbHVlIjoiWmYwdXBmUVVpS1ZpeVZMUElhVzJhcEltRGdDZ3VQRlFwU0U4UGxxQ3IzTmdWYldTeHcyaWVyV0FPM0pzSnFrTTlxWkp4NDJBUTRFTit4TWtReGdCcmkzZThtQ0QxQW9tUnVjZWNsKy8zNzl5YkFxclNrcUVzNGpzdnkzeElMa2QiLCJtYWMiOiIzMmY2NzE5NmE2MTcxZDFlMThhNGRhMGEyOWI0NGM2ZjZiYTc0MGM4ZGMyOTExMDE4ZWY4Y2E0NTYzNDczY2Y4IiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:04 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6IlFzR0VIZmt1NDV5UlR5Z1c4Z0hvc3c9PSIsInZhbHVlIjoicW15ZVFqeG44amtKK0tUSmordk8xVFkva0pVMlhNMjNNWjlNa05wMVhNYkJLbzZ5dDlTL0c3eUNjQTJyNXlYbThuRGdRTWpPNW9vRk5STkE0RG1LdDVUV0MvcXE4WGhFMDBMcWN3V3pPNm1RRFp2emNLaFIzWFhMSm50eTNsZFEiLCJtYWMiOiIxYmQ3MGQzYjU1NzhlZGY4MTg1Y2EyOTRiY2QwNDYzZjc0ZmY5YzFiYjdiYTE0Y2JhYjc2MmFkNGUwYzRmNWQ1IiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:04 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6ImxibzhUQS9sY2pTOVZaV3hsbkZmeUE9PSIsInZhbHVlIjoiVjRDaWdRY3BFTGZmemNFRm9ONXg1dGl3ZUdFcXVHMitJVzBnbHUvMXNBSGd2YS91NkJyYzRudzRnUlc4OGpIdVJwOGNWUVo1QUE3aHBPeFNHZnNsNi9IeFpZaENlTmJzRG1ZcDQ4dnJqaFc2QlBNUzcrbmt6VlVvZnpkd28wNXgiLCJtYWMiOiJkNGMwMTY4ZWJiMTA5ZTM0MTg4NjNiNTI1MjVkYjUyY2E5ZDViZGEwYWY0OWE5MjE0ODUwMzhhNDE2ZDAwMzVjIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:04 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6IlQ0VTlwQ0ROV1NqSGE5MW9oeFF6Tmc9PSIsInZhbHVlIjoiUkFPMWlOKzRmTTBjWVhRV3BWUWlITjNaczlVUElXYjM1Q3BIVjVTVzRaNEN0ZjJ3NHgwWUtiRGxpc3VUNHptN1BaQmJuVVp1ZUtzTUFJVWRYZ3Q1WkhTRGlPNlVMMGZRQkRRNjZyK1BjTFM5Z2hCQUxXcjBkNmJuM3JROHl1QjAiLCJtYWMiOiIzYzljN2U0NzA1NGQwYTUyNzVjZDc3YWJmZjQ2OWQyNTQxZTNjZjRhZTNmMWMyZjQ3MmQxYzViZTE1OGE0NzJiIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:04 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6InZWbFhxVE1MRzZHTUJPZ0FxaWx6YlE9PSIsInZhbHVlIjoiK1E3dWUvT2hxR0s1dGdyT2pNSDlRK1BlaTRTY2pKQ0NDV2s2eWJIdEg4YlIzZWtGQU9SN2VNZVhNU1crVG1wWXYyZnlQYkQ0YnN3bmN0dWRQelRBMVUreUhNOUwyRXJJWmQwS0wxRStUS2VDTlNyczYxTmxRVU83azRxVmk3SXMiLCJtYWMiOiIzNjIzNjMwZDIwZTQ4NTNlNmMwZjE5Y2E2NzYyZDBjOWNjMGY0MjE1NGQxNzI4OTQ3ZWNjYzU5MGJiMmFkMzA1IiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:04 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
    \"date_acquired\": \"2026-09-12T12:42:04\"
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
    "date_acquired": "2026-09-12T12:42:04"
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
               value="2026-09-12T12:42:04"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-09-12T12:42:04</code></p>
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
set-cookie: XSRF-TOKEN=eyJpdiI6IkxzN2I4Tlk2MUlkbFY4Z1ZhQ0FJSUE9PSIsInZhbHVlIjoiMVdRUHV3M0lDK2lRd1dCTDFPU2ZLNFQ0WHpwdGdNRGpCbHBNSjRHVThteVFZU3Y2VmRnaVBiK3ZJTzhlVUVkYmxVVkZ0OWdQQWNwYTI4V0NScHhTRFZaMVE5YVlINWhXSXNjbkprelBrQVRrSHRuZ0Y3Ly9JVXQyakFpWmhmZngiLCJtYWMiOiJlNDUwNjMzZDg0MmJiNGIwOTY3NmY1MDM3OWE0NzYyNGE3MjdmM2JlZTBlYzU4ZDU4NmQ4ZjEyNWRhZWYzZDFmIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:04 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IjlnTmdDN3FDaStoWkIwQjBxMjlrb0E9PSIsInZhbHVlIjoiTGtTTm4xci9ZWGJZOE5KbHhNWWJMQ2pWNHlrTExnMU5NREZBc3lZRi9lb2ZWamk2ei9GcGlwekhqcWQyN01OVzlZSXVjQ1BjU3M5U1I0T3k1YjRzT0sxeVF1Z21IS3J4R09Ud0M3WGNFZTJZd281ekUrK3ROZmZUNWQ5WTlxZmUiLCJtYWMiOiI5YWMxMjBhMmY4ZDMwYTY3ZmE0ZmFlZDEwZmUwOGE3MmNiOGRhYTU5NDk2MjUzMGVjY2U2YzdiNTNmZGZhMzFlIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:04 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
    \"date_acquired\": \"2026-09-12T12:42:04\"
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
    "date_acquired": "2026-09-12T12:42:04"
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
               value="2026-09-12T12:42:04"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-09-12T12:42:04</code></p>
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

                    <h2 id="other-endpoints-POSTapi-mechanic-expense-records">POST api/mechanic-expense-records</h2>

<p>
</p>



<span id="example-requests-POSTapi-mechanic-expense-records">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/mechanic-expense-records" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/mechanic-expense-records"
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

<span id="example-responses-POSTapi-mechanic-expense-records">
</span>
<span id="execution-results-POSTapi-mechanic-expense-records" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-mechanic-expense-records"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-mechanic-expense-records"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-mechanic-expense-records" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-mechanic-expense-records">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-mechanic-expense-records" data-method="POST"
      data-path="api/mechanic-expense-records"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-mechanic-expense-records', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-mechanic-expense-records"
                    onclick="tryItOut('POSTapi-mechanic-expense-records');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-mechanic-expense-records"
                    onclick="cancelTryOut('POSTapi-mechanic-expense-records');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-mechanic-expense-records"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/mechanic-expense-records</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-mechanic-expense-records"
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
                              name="Accept"                data-endpoint="POSTapi-mechanic-expense-records"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="other-endpoints-GETapi-mechanic-expense-records--mechanicExpenseRecord_id-">GET api/mechanic-expense-records/{mechanicExpenseRecord_id}</h2>

<p>
</p>



<span id="example-requests-GETapi-mechanic-expense-records--mechanicExpenseRecord_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/mechanic-expense-records/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/mechanic-expense-records/1"
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

<span id="example-responses-GETapi-mechanic-expense-records--mechanicExpenseRecord_id-">
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
set-cookie: XSRF-TOKEN=eyJpdiI6Ilh2MFUvRGJWRC90QWorRWhEMFROa3c9PSIsInZhbHVlIjoibmQ4SCt2SDhtN2NBRTc3TUxhcEsrdUVzbmtaZnIzSWJWejFTMVhvU0FNTy9oamtwbVE0RHNXZTNhT3dpMEVsMkNRNWJCM0NGT1NFdk1Lb012eHVhZEUyV3JJOGVBTkdra2ljYVJDeWpJV0liYzVkYUduRG44S1RLcmJreDVlQVEiLCJtYWMiOiIyOTNhZDhjYjNjMzIyZTUyMTcxYzE3ZThhZDk0M2M3OTU3ODgyZDk0YmNiYmQ2ODVjZjM0ZjhhNzAwZjRiOTZjIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:04 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6InlPNGlOWUtLVHhhRFlBY3AzTy96dHc9PSIsInZhbHVlIjoidzk1UVp2RkVscE9ZNWY1dEUxVW9ZMjZjVTVKejJMZ1dpeTFPeVVsQ2pIamh6ZFVXaEpWZjJYS3FMdExwWUZ4SXRvNGhQVmZ5R0hnY0Irc0ZMQklwWGUzNjFQOHFtRFRqMUNWaFVYaVV6QkpGWVNYV0hHQ3Q2WjRCZVFmNGpHMFIiLCJtYWMiOiJiZDdhOTVmNzVjYzc3OWMxNDFkMDc4OTE4ZjU1ZGNlYmEyNzhiZmJhNDM0NjFjYTU5ODA3MjNiN2E1YjVkMmRmIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:04 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
<span id="execution-results-GETapi-mechanic-expense-records--mechanicExpenseRecord_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-mechanic-expense-records--mechanicExpenseRecord_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-mechanic-expense-records--mechanicExpenseRecord_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-mechanic-expense-records--mechanicExpenseRecord_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-mechanic-expense-records--mechanicExpenseRecord_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-mechanic-expense-records--mechanicExpenseRecord_id-" data-method="GET"
      data-path="api/mechanic-expense-records/{mechanicExpenseRecord_id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-mechanic-expense-records--mechanicExpenseRecord_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-mechanic-expense-records--mechanicExpenseRecord_id-"
                    onclick="tryItOut('GETapi-mechanic-expense-records--mechanicExpenseRecord_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-mechanic-expense-records--mechanicExpenseRecord_id-"
                    onclick="cancelTryOut('GETapi-mechanic-expense-records--mechanicExpenseRecord_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-mechanic-expense-records--mechanicExpenseRecord_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/mechanic-expense-records/{mechanicExpenseRecord_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-mechanic-expense-records--mechanicExpenseRecord_id-"
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
                              name="Accept"                data-endpoint="GETapi-mechanic-expense-records--mechanicExpenseRecord_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>mechanicExpenseRecord_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="mechanicExpenseRecord_id"                data-endpoint="GETapi-mechanic-expense-records--mechanicExpenseRecord_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the mechanicExpenseRecord. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="other-endpoints-PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-">PUT api/mechanic-expense-records/{mechanicExpenseRecord_id}</h2>

<p>
</p>



<span id="example-requests-PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/api/mechanic-expense-records/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/mechanic-expense-records/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "PUT",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-">
</span>
<span id="execution-results-PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-" data-method="PUT"
      data-path="api/mechanic-expense-records/{mechanicExpenseRecord_id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-"
                    onclick="tryItOut('PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-"
                    onclick="cancelTryOut('PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/mechanic-expense-records/{mechanicExpenseRecord_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-"
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
                              name="Accept"                data-endpoint="PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>mechanicExpenseRecord_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="mechanicExpenseRecord_id"                data-endpoint="PUTapi-mechanic-expense-records--mechanicExpenseRecord_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the mechanicExpenseRecord. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="other-endpoints-DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-">DELETE api/mechanic-expense-records/{mechanicExpenseRecord_id}</h2>

<p>
</p>



<span id="example-requests-DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost/api/mechanic-expense-records/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/mechanic-expense-records/1"
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

<span id="example-responses-DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-">
</span>
<span id="execution-results-DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-" data-method="DELETE"
      data-path="api/mechanic-expense-records/{mechanicExpenseRecord_id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-"
                    onclick="tryItOut('DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-"
                    onclick="cancelTryOut('DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/mechanic-expense-records/{mechanicExpenseRecord_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-"
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
                              name="Accept"                data-endpoint="DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>mechanicExpenseRecord_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="mechanicExpenseRecord_id"                data-endpoint="DELETEapi-mechanic-expense-records--mechanicExpenseRecord_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the mechanicExpenseRecord. Example: <code>1</code></p>
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
set-cookie: XSRF-TOKEN=eyJpdiI6ImU3Yk1pTUNFWC9YVWdONTJ4T3lTaWc9PSIsInZhbHVlIjoiTGNvYWdsMUtTaUNXRGcwcGFKOEtjOEN4YlJJbkpTZjh3TXU2WklEYXZjcjJVVVdnR2U0Z3ZWem9WajRrWExZaGI4ck5rSlRGSnYydUhXUncxM1J4bURHeTE5MkZqbEJSdmVlQTBKTWRaWTY1ekNpVzhHcWRaZW5tUEtRK0xXSnUiLCJtYWMiOiIwOTg0MmI3ZjQyYmI1ZWNkYjVmYTNlYjE4YTg5MjYzNjc0MDc5ZGFiYzcxZDE3YmRhZmFlYjZjMWQ2OWYwYTlmIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:04 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6Inc1SC9iY2RvckhkY1pQTjFDT2NVTXc9PSIsInZhbHVlIjoicGJYdVAram1CaUdWVTVoNENpN05zUTFrajI5V1lmenowRHJIby9YdlBmMjZqeGJqOVlkRS9JRHhXQmdDNkQ0N2F1ZWk3dll4MkNhcFFreFpjTXVGNm5xZ0JZdjdnNENiVXJWenJkcGdlUzVCbHlaSVBSVmpXZHhNbXphRWgya0UiLCJtYWMiOiI2NzFhMWYwMjVjNjFmMTM3NTZlODc3MDhlNzJiMThlM2I3YTc2NGQ4YzU0NThlZDljYTcwMDcyMTk1Y2YzNWE2IiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:04 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6InB5eDVKSG1RQjk2MjgybFVxOHRMREE9PSIsInZhbHVlIjoiMlpPck1DVzhHTnVWSWlHU1VUTFBjaS9QSWh5WEVhWTExMVJZOHJndk9lWGp3NnV1bXJjYjNVV3c2WkRtR3o5VDU3YS9DRVZDTlpvY1pHYnVTQ2NOOUJjQmt0cGNzRUMvajNaVGVkS1dMTnJyclRuR3Q2NXJCQjhpU0xHQ1hxRmYiLCJtYWMiOiI3NjU3MTFlZDUxYmFiYzQ1NDc3ZGZmN2UzODhlNTIyNDVjZWZhNjgxNDQ2Y2E3MDRhYzc5ZjgzMjMwYTI0MjFhIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:05 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IjJyKzV4NHczWHR6TGo0WURaMWc4YVE9PSIsInZhbHVlIjoidy8zSDl1SUNBNmhCNGQ0Qkl3OWVsL05NUDdkbGU1VzNZQmErV0pBQVpCKzRpbU1SNU5kN29PbWNodHZtN3hjZ3N2V0V5Z04yVWZpNk1LcHFBZGVvaUFCczFIaklLR1pRL3NFSFhqZTVVSE5KdmNDbXAySTAzMXNwMUpBZk1FdFUiLCJtYWMiOiJiNzE1YjU1ZTFjNTZjOTlkZGViNDQ2MzFlOWMyNWIwMDczNmRhNzEzYjFlOWIyODJiODM0YzUzOGU3N2QxMGY5IiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:05 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
    \"budget_date\": \"2026-09-12T12:42:05\",
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
    "budget_date": "2026-09-12T12:42:05",
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
               value="2026-09-12T12:42:05"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-09-12T12:42:05</code></p>
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
    \"addition_date\": \"2026-09-12T12:42:05\",
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
    "addition_date": "2026-09-12T12:42:05",
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
               value="2026-09-12T12:42:05"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-09-12T12:42:05</code></p>
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
set-cookie: XSRF-TOKEN=eyJpdiI6ImYrRFZIUkNWMWxPN0k4eFJKaWtycXc9PSIsInZhbHVlIjoiY1pQV2ttbkpqYld0cWxhU2tjdW1iemVENTlqZDlWdXl2b3h3N0J2N2htR0REamRtQnlRb1BvWHptK2pURW1HYUZaNFp4RWEvZmNwUE9UZ3hUa2p5M2t3aXJYZitldFN3R2lFWnhkaXRBY2owdnpKWERzUi9sWFJNSGNwZ2xTU1MiLCJtYWMiOiIwNjZkYThmZGYxOTZhNTc0ODk0ZTM3N2QxNDAwN2ZlNDY2ZTc5N2VmZGY3MzIxZjQ3NjY5M2FmZjNjYWZlN2MxIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:05 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IkEwWmRFQ2hCcnNqYVhhOVdKOGR6S2c9PSIsInZhbHVlIjoibTFDZFFNZExQeFpWelpFampIVGhmREdQQ2d3Q1dLa0lMVlU3em1ub0tmdy9DVXpuSzBsSGJ1dFh4VHVtWWVqZ25JYTZ6MG9RSDhydkhjUkVEdFEwWFc2QkNMUktKYkIrQTFVVkJzYUtTWmdCVmtmUy94QnNiQzFLY3IrS1JMQSsiLCJtYWMiOiI2MjNjZWJlNWUyMjEzYjg1ZjA5YzAxYWUwYTMwNDExNzExNTRiZTUyZjc4ODNmZDhiM2M5OTk0NjkxYjE2NWExIiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:05 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
    \"addition_date\": \"2026-09-12T12:42:05\"
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
    "addition_date": "2026-09-12T12:42:05"
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
               value="2026-09-12T12:42:05"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-09-12T12:42:05</code></p>
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
    \"budget_date\": \"2026-09-12T12:42:05\",
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
    "budget_date": "2026-09-12T12:42:05",
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
               value="2026-09-12T12:42:05"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-09-12T12:42:05</code></p>
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
    \"entry_date\": \"2026-09-12T12:42:05\",
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
    "entry_date": "2026-09-12T12:42:05",
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
               value="2026-09-12T12:42:05"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-09-12T12:42:05</code></p>
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
    \"date\": \"2026-09-12T12:42:05\"
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
    "date": "2026-09-12T12:42:05"
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
               value="2026-09-12T12:42:05"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-09-12T12:42:05</code></p>
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
set-cookie: XSRF-TOKEN=eyJpdiI6ImV6bWQ0S1QxL24yM3R0YWtEbXBoRHc9PSIsInZhbHVlIjoicm1kMlhJNFFlb2hDOEszd1NUTFYrM0RubnIvYnpuQTEwbFdUcnlERzVFek5rZHRwTkpHZkFDSE1MUUJqVENTK01BN3JWWWY1TUpQZkkxUnJGYlp6cTg4SnZJaVB6WmZRUmFUN1RxZGJWUWVoYWgrVTN5MmY5VVVJOWs5V3c3TXgiLCJtYWMiOiJkZGU4MWRkMTdhNGJlOWUwY2RlOTZmY2IwNjUyZTM2MmI0ZTE0M2ExNzZlNDdmMmQxNjZhZTVlYzQzMmQyNzY1IiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:05 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IjNjek9VMEY0UEt3cnBxbTlpU2trZUE9PSIsInZhbHVlIjoieHJpK2U5aVpib3BaUmZPYlpJOU85UnZDYVBzWXVVUlYxYWQvWWIyMkJjbUpuNU5aRW9BNHpLeTRZOHFRRHc2TWRyWGxPZEhCd0xEa3lQNEMrQTFEQkFrQ0dOZE80QVI1Yi84UDZZY252Wkh3RUgyVWZPNHQ2Vi9YbFNhRytrUFYiLCJtYWMiOiI3ZWQ0ZWU1MzdhZTZjNGQ4ZjliOWFhMjk1ZjJhYmM2YjBkNzcyM2Q4MWM3YjlhYzFkYWQ1Mjk0OTAwMmFhM2M1IiwidGFnIjoiIn0%3D; expires=Sat, 12 Sep 2026 14:42:05 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
