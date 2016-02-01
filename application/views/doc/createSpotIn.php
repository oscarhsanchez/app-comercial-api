
<?= $header ?>
<?= $sidebar ?>

<div class="col-md-6">
  <div class="how-to">
    <h2>Create a New Spot-In</h2>
      <p>
        You can create single spot-in (Ckeck-In) using this method of the API.<br>
        The Spot-in is composed by the following elements: <br>
        - User (Mandatory)<br>
        - Tittle <br>
        - Place (Mandatory) <br>
        - Latitude <br>
        - Longitude <br>
        - Image or Photo <br>        
        - Group of users on the same place (Mates) <br>
        
      </p>
      <p class="note">
        Comments and likes will be added after creation. Mates can be added during and after creation.
      </p>
      
      <h3>URL</h3>
      &nbsp;
      http://localhost:89/admin/index.php/api/doc/index/spotin/createSpotIn
      
      <h3>Method</h3>
      <p>
        POST
      </p>
      <div class="params">
        <h3>Parameters</h3>
        <table>
          <tbody>
            <tr>
              <th>Param name</th>
              <th>Mandatory?</th>
              <th>Explanation</th>
            </tr>
            <tr>
              <td>api_token</td>
              <td><strong>yes</strong></td>
              <td>This is your API key</td>
            </tr>
            <tr>
              <td>channel</td>
              <td><strong>yes</strong></td>
              <td>im</td>
            </tr>
            <tr>
              <td>utm_campaign</td>
              <td>no</td>
              <td>Name for the campaign these messages are related to (if they are)</td>
            </tr>
            <tr>
              <td>messages</td>
              <td><strong>yes</strong></td>
              <td>A JSON array of messages, encoded in Base64</td>
            </tr>
          </tbody>
        </table>
      </div>
      <h2>How to generate the "messages" parameter</h2>
      <p>Param "messages" is a JSON array containing messages to be delivered. Each one is a hash with the next fields:</p>
      <ul>
        <li>country_code</li>
        <li>phone_number</li>
        <li>simple_text</li>
        <li>sender_name[1]</li>
      </ul>
      <p>
        [1]
        Name of the sender, for messages sent via the SMS fallback
      </p>
      <h3>Example</h3>
      <pre>[
        {
          "country_code": "34",
          "phone_number": "628712943",
          "simple_text": "A test message for my favorite customer!",
          "sender_name": "FOO"
        },
        {
          "country_code": "34",
          "phone_number": "699142653",
          "simple_text": "Another message, for another customer...",
          "sender_name": "BAR"
        }
      ]</pre>
      <h5>
        This JSON may be encoded in Base64 and "cgi escaped", then passed as the "messages" param
      </h5>
      <h2>Example of a complete request</h2>
      <p>
        This is a working example for both IM/SMS channel. Run it from a shell script or with your preferred REST client :-)
      </p>
      <pre>curl -X POST http://www.uberpusher.com/api/v1/messages/create \
        -d api_token=2d92497254701d5ca623ddd1163a1b759d3ccbb9bd94b99ce3 \
        -d utm_campaign=A+test+campaign \
        -d channel=im \
        -d messages=W3siY291bnRyeV9jb2RlIjoiMzQiLCJwaG9uZV9udW1iZXIiOiI2Nzg3OTY4%0AOTQiLCJzaW1wbGVfdGV4dCI6IlRoaXMgaXMgYSBzaW1wbGUgdGV4dCJ9LHsi%0AY291bnRyeV9jb2RlIjoiMzQiLCJwaG9uZV9udW1iZXIiOiI2Nzg3OTY4OTQi%0ALCJzaW1wbGVfdGV4dCI6IlRoaXMgaXMgYSBzaW1wbGUgdGV4dCAyIn1d%0A</pre>
      <h3>Response</h3>
      <h4>
        If your messages were successfully enqueued:
      </h4>
      <pre>{
        "status": "success",
        "enqueued_messages": 2
      }</pre>
      <h4>
        If your messages could not be parsed (ie: not CGI escaped)
      </h4>
      <pre>{
        "status": "error",
        "error": "malformed messages"
      }</pre>

  </div>
</div>
