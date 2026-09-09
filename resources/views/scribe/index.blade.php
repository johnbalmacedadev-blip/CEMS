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
        <li>Last updated: August 16, 2026</li>
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
    --form "attachment=@C:\Users\VJ Kyle\AppData\Local\Temp\php30C9.tmp" </code></pre></div>


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
<p>optional File attachment (jpg, png, pdf, doc, xls, zip — max 10MB). Example: <code>C:\Users\VJ Kyle\AppData\Local\Temp\php30C9.tmp</code></p>
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
set-cookie: XSRF-TOKEN=eyJpdiI6IndzcWFwQ0J4eStNRzVubWhZd2lTM0E9PSIsInZhbHVlIjoiSGUwbVd3M0U5ZEFjWnRlMFMvMjhqckdGN0hjYnlVTmZXME1qdU40MjhHbnZlclpGaTk5VTh2VkQ1R1U5UkpHTEp5cVRLSkwzV0J1ME42WFdzalU1SUZDTDFqcGJmaVNpb2ZXWWZsd0F4R3ZGY3dDQndkL005RENJM0xMVm5VN3MiLCJtYWMiOiI0NDNhZDFlZWE5OTQyYjI0NjkxNDg4ZTJjMmExNzdmYWEyZjQ1NGQzMTFjYTRjN2NkZWRmYjQ0MTg1M2EyZWEwIiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:57 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6ImVzMFFnN3YrRktMcWo1eUxHTkFlZ1E9PSIsInZhbHVlIjoiK085N1h4S1ZTRkN0OWQ0UEczcTIwSmI5Q3RyeHFYWVhiT3gvUjgxcHBubWI2clV6U2VDalpyR2FQZG5wMTNzS2tpYzZrQmRGL2lSOFQ4NWp6eHVuZERuclowbE45aWJRbE56RW9TM2poVzB5UUVFUWNiblkxY0lJSSt6Tzc4bWsiLCJtYWMiOiIzOGJjMjRlNDRlNTk3Njc1ODUxZGQzZWI3MzkxNDYzYTRhMGIzYjgwOTRjY2I4NjhjMjEwYzhkYmVhYzZlZWI2IiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:57 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6InRGNWlxRWNDWFllVkZJdUc3azRXQlE9PSIsInZhbHVlIjoiTjBzaGU3K0EwVHRiMXYraW1FL3RjVzNOM1FRSUI2TCsxNCs3dWNlV2kyUldkYkFRdC9VNmxsVnAvZEpHZkp3bE91ZnJNTmhFWmRwZThUZnZ3WTFZUFN2dW5IVC8zd1BLZmtwS1lUUWtxUEVnYTc0Y3JpRDgzbTIxdnpIeFFpRGciLCJtYWMiOiIxYmI0NDM0ZDBiNGQ3M2FiYTI2MjJmY2E5ZDExMWFkYjBjMGFmZDljMzQ4OGE0M2Q4M2RmNmMwMGQyMzkzODY3IiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:57 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IkhHaEZOK2l3RWQyUDI3di9zWHVRYmc9PSIsInZhbHVlIjoiWXc1dEVKVk9WQWxWNGtuM1NUWUVKTWhtb0ZKSkh1ajNVOXVQeUNRSGNHWDZDaHFJM1U1RnVJb0dpSXorZVVmcEdDcnExVFFkNjZVMThrd2NKV2NNL2U5WlRiaVhUZkphWjFYVkMvUUloelFERFgyUzhNOUV0bGxDd1FxZU5FOFAiLCJtYWMiOiJkZjM1NTdjNzllNWJhN2UyMTQ1YjM4N2ViOWQ3MmViNDRjOTdmOTQ2NWM0MmNlNDc1ODE1MDc3YTU3YzMxOWI3IiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:57 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6IlBjS094Vyt2REo4bFJJTGM3SmRKUlE9PSIsInZhbHVlIjoiZ3I1SFptUTgwUXFRTFdSVGZIN3Q5dDlLdUs5djJyU3NZUEx0Y2FOSzdnYnYzZHFxVklIdDgyMVU1eVZOUjNvVkhjek5XMlVFeC9qcDZncGhHeUdFZkdhTGIyY0pjTUNWcWZKMzRYZlY5QnpXWTVaNWNvNUg1ZnE1S2xpSnRDcHgiLCJtYWMiOiI4M2EyNGZlNmMyZWRiMGNjNmZiNDY3MGQ1NDg0NGY4MTFlNWQxZGNmMjg4MjhiY2MxZmNjNDhhOGViZmE5OTQ3IiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:57 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IjY0SS9PYzI2Wk1vT0hkKzF2LysweWc9PSIsInZhbHVlIjoiQTZjREhNY3AyQlhXTVY5dUZCbkF3ODVqZ29uZ3N5dVNUcmVsamMyZHpVZmhScjFMb25GSi90TGl2OGQyV25IU1hrZ0Z6TFZ5bTFXNnJGQUN3Lzk1TmNETWJqQ01YSFlOditDVXRmb05ibHpDUGlGODR0d2s3TEhTalh4Sm1QVWciLCJtYWMiOiJmMjA1NGI4MjQ4MjFlYTk2Y2FjYmI2OThjMzFmZDE4MDBjMTI0NTZjMWQ3MTkzOTFkNzY2NGYyMzE4Y2IyNGI3IiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:57 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6ImU5Qm8weXg0cldBZmprSlo5SW1ucnc9PSIsInZhbHVlIjoieHNYdUtzS3A5Y0pMdmcweWNCbldQZ0hTTjRvQndkd3YvNUtLWUVURnhzeFVMM3hCWmhzY0dkdVBXWmpEMVZwRGZGRVowWk5FY2FQdkFCUEVEZ2tzdmZXMDU3WHltTm1yV0krUXU4QzJKbWZmSGEwWlZKVFBxUHMwV2xXbUpnSjgiLCJtYWMiOiJjOGU5NDg2YjM0NjgwMDhhNjc2MDk0NzU2MmYxMmE4MzU5ODI5N2Q5OGEwMmFlNDcwNjRiOTJlMDUzNDQzZmYyIiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:57 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6InpUV3N3cUQ4RWd3QXhYZGtHMkR0aHc9PSIsInZhbHVlIjoidkZyRkIvdnVOL3ZhR1JGaS9kTlJtcU1VSjQ0R1lJN3VRdERMUG1ncXJtMVRMbURhZFl6SFluVTM3cjBNbXVEUkp6b3V2ZE9naFUvTmVQV282SGE5cmZPa3UrL2xqTmcvN1d2Nk1mbHVta0ZCem95RzhoNDBDT0cvc2ZTc0V2OTMiLCJtYWMiOiIwY2JhMjNmNTRlZDhhMWIxYTkxZmRhOGQ4MDY1MzcxZDg2OTY3YWQ1Y2Q4YzIwNWQ5MGFmMmJmZWY0MjE3M2EyIiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:57 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6IjlSTXhlRHBFZEJCYm9IekFqSG55OXc9PSIsInZhbHVlIjoiaERmUjJRcjFTdUZIT3FFbzFGU0xlVXJ2eXgvYWRjeUN0UTgvbmF2WEhDa0tMVVBCTGhFTzR1QlZqZERpei9DK2Q5WlBUVXcyWHk1VXFuNEduOUxiZkNxajI1ZmVhUTFWNXlGNTBpYXhVUFFqT29mQ3FqcW0walk3Zk9hbUVsM1AiLCJtYWMiOiJjYTlkNTMyNzg0OGEyNGY2NWJkMGM4YWIxYTlhNTVhYmIyMzAwMzhkZWEzYjAwOGQzNjdhNjg1Y2Y2MjdhMWU3IiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:58 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6ImtMdkZVT2pWOXFjZWw2NHhtQWJvS0E9PSIsInZhbHVlIjoiM1Z0OTZZditZS21GWDRiN3lVUlIwaEFwSThVeG1yK1JWMit0OEJkMVRnVlVZb1JTNTc1NWRUTThEK002QVBQZURHSjY1cEp1eHhEbFpQL2RXN1pnZU9JN0VuQk5GRmRzc0QyQ0huem1vVmN3NVJDTTQ1aDZrT1Z4Q1NRQjROcFUiLCJtYWMiOiJmZWZmODc0Y2E1M2Q1ZGY0NDQ2YTljY2JjYzhkNTdhODdmOWY3NTUyY2IyYjY0YmNjNDE1YjE5OTZlNmZhNTZkIiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:58 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6IlhwaFVsR3dwdlNUU3EzMVB5eEkwcEE9PSIsInZhbHVlIjoiNVMzUGNmbTZlUGNWd1dMeGc5Sjg4cC9SZzN3SDNwTzlvUjZQM0F6M282YkFkejlkdGp0NWtTREx4azZ5N2toNWswdm5sMUZEeE5Ya2tSRGgva3poTXdFN1pxUFNtU0lkSExYTUMrWEVnbmF6dlNLb3YzLzRpMlJNaG9YcGZFZk0iLCJtYWMiOiJmZTAxNTI2N2M3ODI3ZmFkMDk4NGM0M2U3ZDEzYTQ0ZWZiZjNiOGExZTllMjYyNzUzNzdkNzRkNzM1ZjkyNDRlIiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:58 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6InZWZEdJaG8zbVlxQjBXZUNBS2RpSnc9PSIsInZhbHVlIjoiU2tWd2VFUWxBTnkzNkxDU2NkeXVVWmNiaXNiV2pPMkxkNlNpSFU2aXdFdFpkVnIyRWVXbE1DWFpmeWYzY1pVWWFqWHlWZDlBSzE3d251bzA1cUVlYmdiOC9HazhnbW9KbmFRa1g5NHVvcGE3alhvVE1zOEdEODROOVVqV040ZVMiLCJtYWMiOiIyNGI3NGFlNGJhY2M2YTJiZjRhYjlmMTQ5NGJhMmVkODBmMTYyZWE5NzA3MmEzMzhkY2VkOGI2M2JjYmIwMDNmIiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:58 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6IlRqaXlnUmYzTG9PaW4zMm9ZVFQ5bVE9PSIsInZhbHVlIjoiaWdoREVQV2Rhd0V6OG0waTk1Rll3UGREeEp6amUrLzhybVJFTVh6Nnkrbk5MdUhsSEtlY25vQW1SbGhudW9ESG92c1dZVDJaVWtMN1F1NEJMMUFIS0xQT2pIRVlYeGFzVWNVaTNiS1N3SFp5ZHdLYkRaQmhlc0F5TFVFL0hFMmwiLCJtYWMiOiJlYjViYmMyYzkyMmJjOWZlYmY3YmQ3YWIwMGNlNTllYjM4YzNjY2FkOGM1NTU5ZmI1YzBlN2VmM2ViODE5NTFhIiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:58 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IjZHWHZHSnJaRGxOUjBPaUE3WWQwMmc9PSIsInZhbHVlIjoiTEx5SEwybHJrUit3cmdqb1ViNTYrVUtXOWpYTDVBYXN0YWlTNWMyWkJnOExsMnhZcHBNVHZySmxEZ2F2Yk52VFdnWVhHWlRaUmtzQ204UFFyWFNjOHUwdE9sbzdlZWdNQ2hzVW0yM0U0bkc4UC9NT0twejNNTC9Wa3hHQTlHZkIiLCJtYWMiOiIyOGMzNTgzMzYxOWEzNjFhZDM3MDdhN2IwZTc0MWI3OGU3MjI0MWMwMDk5NzVjZjUzMzgyNGI1OTliZWEzYzJhIiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:58 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6Ik1mNEhYMDl1S1hLakxObVMzODFGMFE9PSIsInZhbHVlIjoiZGhwMzN4N0JIbTJjWFdINDc2enBTL3pLbXR4cUx6emdYQm9HZzdHQUEwYnc3eE5WTm5jRTc5cTd4dHRjV2I4a0xwS2pFTUFLUytkSndHdEhFWmlNaHNBWllrdUkrZTdmYXVWTWtvUXZleXUyL3NZaU5nSSt6elJscDY2SytBeUYiLCJtYWMiOiI3MTgyYWMxYzUyYjA5YTVhNmY1NTBhNGNiNTcwZDE5Yzc3ODEwOGUzMThlMjU3YzM3OGVjNWU5NjcyYzJlOWQxIiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:58 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6ImFvVDlKRUxJMzN1V05LOCtpNjVJOGc9PSIsInZhbHVlIjoiY1liSlNybExPOU9sYU5WUWtmbitTOUFoaldhaUgrZ2pyY1pLWUtta1Y2K1YrK0FQWW8ycVQzaWhlcVV1U3d5VlRQN3k2TGZaTk9pSkhBRmVvMHJQNVpQT2JKWlkyMjIyTm9mdGNuYlFRRHZsZEhsSEl2aDA2UjFXcjBBYWlKYXYiLCJtYWMiOiJiNTc4ODM5YTJkNGJlNjc1NzI0ZTEyZTRhZjEzM2YxZTZjZTFiMzQwNTIzNDRkMjNmZTc0OWZkODk0YTBiY2U3IiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:58 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
    \"date_acquired\": \"2026-08-16T16:09:58\"
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
    "date_acquired": "2026-08-16T16:09:58"
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
               value="2026-08-16T16:09:58"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-08-16T16:09:58</code></p>
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
set-cookie: XSRF-TOKEN=eyJpdiI6IlRqN3RTZGdLb28vV2wrY0tXcGlWOVE9PSIsInZhbHVlIjoiM1JmbFhXR3lXQlFmSWMrY3lTZzYrS3I1UCtSM1BiZWdNZm1kN1ZRc2lEWlVMaGJJVjg2WDEzdDBzYm5xU3Q0VlBoa2xNZEgrZ292SHF0bDZSeFNycGg4aTl4NnJPRFY4N1cvYVNnekdVaXpHK0RKN0tXSUlNRmNRK2x5N3ZpVmoiLCJtYWMiOiJmYzI1N2VlODMwMGI1MTY3NmUwODQ3ODBjMGUzZWQyMjdkODY4YjczMDE3MDBlOWZiMjVhZGY2NDMxNjAzZTQzIiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:58 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6ImlOVUdFcXdsNS92K1p1eHlNRHY3M3c9PSIsInZhbHVlIjoiVmttM0pFdHlZSnAwQ3RPOFN2bUc2MXdXcjN5dnhDRG1VeldMeWl4aDU2OWYyUHRzYXNWM09acHBESzcwQ2NoMnBxaXVaREhaMVNNZWp6UVQ2S1V1UWZ1NlpjZEtpbnRXV0MxTmtXU0xLaktCdDY4YWJlTkhiSUk5SlFOTGNhY2YiLCJtYWMiOiJkNDE5YzY0ODJiMTM1NzM0MDI3NTMwNzA5MDU1OGNjNzZjZThiNDg4NzYxMDMxYmY0YTgzNGZiMWRiZjc3MjA3IiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:58 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
    \"date_acquired\": \"2026-08-16T16:09:58\"
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
    "date_acquired": "2026-08-16T16:09:58"
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
               value="2026-08-16T16:09:58"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-08-16T16:09:58</code></p>
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
set-cookie: XSRF-TOKEN=eyJpdiI6IndTUU0zakk1UGsxL29JZE9aUDZWUGc9PSIsInZhbHVlIjoiRlNKdWxIaW9Jc05MTXBTL3JvK1krNzN5a2xZYWsvUzFsWWR2bENkWmJrV0tYZDZISVFqSGRxeGYxalN5Q0J3bTRiQ2JJKzRCNXNzK2NFK01rZmVScU1wdENiVk9FeGRYWHA2QUZTYzNWNkllWlU2R1BQTEFtOG45Y2NvTVoxM1giLCJtYWMiOiI4YjhhM2Y5NTNjNWM2YTMwZGU1OGQ1MmYwODllZTQ1MWVlZGUyYTM4MGNjNjlmOTNkNmRkMjA5ZmZiNDhkMGQ4IiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:58 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6ImhsRlVLTFFJaVNOQU5WM1B1eXArUEE9PSIsInZhbHVlIjoiTlpkZ2tDc013b1cwSWlWOC9NSVgwazdPQ0RyQ2cvODQvR3FlSHFaRFE0NWJzQlpRMUFOQWZzbkNQMjU3c01XdWo0Uk05dFNKaSs2R1NMUVZZcVA4SjRNYWxNZVZnMk1CN0tXczJHaFpVdTFKOGRRenAxa1VCU3Q4YWY2WnltbW4iLCJtYWMiOiJkY2M0NTU5ZTM4OWFlZTM1MWI1OGYxNjhjOThlNDlhMWQxOTU3ZTVlZGNkMjVjNDVhMmJmMGFmZmUyOWE3ODdkIiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:58 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6InVLVTgwNjZVYnVWRytESm9hTEdNRkE9PSIsInZhbHVlIjoiTmVkd3NiV2ljRys5MXRFdFozRzNESjZ6NTAyQVh5L1RFbWZhb1VJRjU3WjIxUFgrcUVTaEpnZEcyM3RKd0cyR2F2dzlnS1pIaGprNkZIckRNYWZnVXcvZmFmTFpKYXIwaVJsU216ZHlZYVlPRGd2ZFF6L0VZNXVNWGwwc0FHM3IiLCJtYWMiOiIyOWNkZWEwNjVjMjlhMmE4NjlkYTNmZTYwZjA5ZWJkZmI4MjcyZjg3YjZiZDk0Y2Q5OTU4YzUyNjhhZWE1NTkyIiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:59 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IlAzckFQak90K3ZBMWdEYVZ5YkRiM2c9PSIsInZhbHVlIjoiY1hhTmEvNWp2WndVVXVGVGsvbERhYTlBbkZ3TUxYNWRQam15UjBMcWdLQlN0RFdNVk83ZnB1aUVSbXNOYjlSMmduMFpjWVRvQUMweW01RDZZWVk5VEtpVzFYOGtmQnE3ZlZiYUx3TEFJYXE5dXlMUWJURTA4TEZ5Rnc4b0dPNlAiLCJtYWMiOiIxOTI4ZTZlMmY4YmE4YjRjMTlmMjA0MjUyODU0NGE5MDQyYmQwNmE2ZTViOWQxYTJlODA5ZGI3NDRiMmQ0YTc2IiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:59 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
set-cookie: XSRF-TOKEN=eyJpdiI6Imx2TmhUWDAvRmJkQkpKS3U1T2JFNHc9PSIsInZhbHVlIjoib1FXWGNYV3pOTlJIQWNuZk9VcmtkMkhCZUxhZ1I0c2RMQUR5TG8wREttaDN5WDZ2em1WMENRR2hFT3dwN3p4aGhPa0g4Sm9Yd2NIMWU1cTlvUkwvNXRPZFZBYW5Sais2NERvK2MyVkxLVGdUWTBOQit3WHdhRTI0L3YxL2pDVngiLCJtYWMiOiIyMTgzNWYzMmZmMjEwZjBhMjExNGNlNzY2NmI4OTU2YzQyYzBhM2UxOTI4MWQ1MDI5MDAxMTE0YzZhMzMxNGQ0IiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:59 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6ImhJaFJ2TzA3RFE4Z3dmZjJzR1NndEE9PSIsInZhbHVlIjoiaEtJcEM2WUNDVGtld012MUtMZW9zU1VXREFpelc4K2MzVm5BT0JFK3lPTGxrc3h5R2xJYU1ES1FuQUEwelkwMTQzK043Mk1qb3RJQjdSb1JtVlA1WFV4TEhQcTR1bDEyYUJFMDBVVlowZ1pDVnNhQ09uOXlzak5VS2xOWWIvaXUiLCJtYWMiOiI5OTAwNGQwMWQ2OGQ2Y2QzMTk4MjYyNzNlMzVmZTM5Yzc5N2Y3YzU2YTdkNDEyYjRhOThlN2RmM2FhZWE3ZjAxIiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:59 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
    \"budget_date\": \"2026-08-16T16:09:59\",
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
    "budget_date": "2026-08-16T16:09:59",
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
               value="2026-08-16T16:09:59"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-08-16T16:09:59</code></p>
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
    \"addition_date\": \"2026-08-16T16:09:59\",
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
    "addition_date": "2026-08-16T16:09:59",
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
               value="2026-08-16T16:09:59"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-08-16T16:09:59</code></p>
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
set-cookie: XSRF-TOKEN=eyJpdiI6IkhIREd2ZXhJcDJZcHVCVk0wTXROVEE9PSIsInZhbHVlIjoicE1oZUhmRDMwQ3dkcGI5YWx2RWovSVh0QjBtZVZPSGdBRXB5c0tJQkhKQWttT05SUWYrYkNlcFJMbmJXZ2VEaUloN2hGQTc2YXZzUkdMOE9pQ1B4c0NBZVM1TGNnWGN0Qnc2WTlyeGZIVWNHVVJiTlN3akRRM2JlWXNmeUYyV1giLCJtYWMiOiIwYjNiNTMxN2U5ZTRmMjBmNmMxNTk0YmEzNjIyNzZhYjFiZDViMTQ5ZDhhZDczN2MwZWE2MTUwNjlkMDRkZTA4IiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:59 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6IjF0Q3NJYlBDZ1Z1NEV1bEhHK1lKd0E9PSIsInZhbHVlIjoiNkI5VUtlU0xZU25YdExVdjlQbkV0L296aWJKWEV3NlpjT2hvNGFJZ2t2OEV5R1dZRlJBb21Ebnp4MzZFV2owbmxPaWpWNXZFQWZUK0pnSFFQRVZWeWk1T1VjYitsa2xMSXJLMHRIZDFtQzBsNGNvZGljQXJCbGJnbnBzVmh6eWciLCJtYWMiOiJiOGM5MjBjNTBjOTAyZGI3MWE4MTRhOGU1NzgzNjA2MzFlMzE2YTUyMjZmYTZhYjQyYjUzYTg2ZTI1YmQ4MWFhIiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:59 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
    \"addition_date\": \"2026-08-16T16:09:59\"
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
    "addition_date": "2026-08-16T16:09:59"
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
               value="2026-08-16T16:09:59"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-08-16T16:09:59</code></p>
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
    \"budget_date\": \"2026-08-16T16:09:59\",
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
    "budget_date": "2026-08-16T16:09:59",
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
               value="2026-08-16T16:09:59"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-08-16T16:09:59</code></p>
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
    \"entry_date\": \"2026-08-16T16:09:59\",
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
    "entry_date": "2026-08-16T16:09:59",
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
               value="2026-08-16T16:09:59"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-08-16T16:09:59</code></p>
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
    \"date\": \"2026-08-16T16:09:59\"
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
    "date": "2026-08-16T16:09:59"
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
               value="2026-08-16T16:09:59"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-08-16T16:09:59</code></p>
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
set-cookie: XSRF-TOKEN=eyJpdiI6IkRhVU9MSHVGVnM2aSt6eFFlckc0SVE9PSIsInZhbHVlIjoielpNRTZIQXRBTkFMTWdHdHZUK0dlUnlRODk4TTl0UUNBR2M3dU1TSEtnbzhoemVFR2FoSWxDQlVZN0E1V1k4OTRDUjRjMGppRHpqOWtGTHZrNU56VkJmRCsyWU5DeHdxSXloU1phMGxZNjBQNDRqTWVwNXBJQUYxdUJramp0Q0wiLCJtYWMiOiI3YmYzMTIzNmIyOGZlZDhkM2U3OGY0ODdiNjA5ODUyZDljMmE4NjAxM2EzNGZjMDhlYzI1YjQxNjVmNWEyZTZmIiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:59 GMT; Max-Age=7200; path=/; samesite=lax; car_empire_management_system_session=eyJpdiI6InJlNm16QXNqYmlhSVRod25xNFFnclE9PSIsInZhbHVlIjoic05Jem9QSE5hUWNBQVdIQVdJcGt2dmdTbWhEYmlnRGFXQ0g5NGthcE5uektxem1rWjlGdDhhVnBxRStkcU9xbGJBOVZaWUFhTVFTdVNMQ1hBYXFsc3Y3VENvbU1VdmZtSXBWWDQzSUJWVzEzVjdhS3FTd1dDc3FIUE53aXlUaDkiLCJtYWMiOiIxMzdjYTE1ODdlNDMyOTA3NWRmNDg2NWM2MjExYTI0MjZlZTZlOWY1ODA4NDRhODM3YjdjZjNkOWYwNjRmNDBjIiwidGFnIjoiIn0%3D; expires=Sun, 16 Aug 2026 18:09:59 GMT; Max-Age=7200; path=/; httponly; samesite=lax
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
