# CaptureTickets
Allows you to display your tickets captured with browser extension

<h2>Installation</h2>

1. Enable developer mode in chrome and install the capture extension.
2. Place the tickets folder in root of your local webserver.

<h2>Creating Filters</h2>

<pre>
{
   url:"",
   body: {
      key:value,
      key2:{key3:value2},
      key4:[{key5: value3}]
   }
}
</pre>

To access key in Object use ->.

<pre>
body->key
body->key2->key3
body->key4->0->key5
</pre>

Perform check for wildcard value match again key's value. 

<pre>body->key=string*</pre>

Perform check for boolean value in Key

<pre>boyd->key=true</pre>


Edit file tickets\readticket.php to add new filters.

<pre>addFilter("myfilter", ["url=*secure.helpscout.net*/conversations/*reply/", "body->ticketID"]);</pre>
