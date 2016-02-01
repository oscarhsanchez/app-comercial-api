
<?= $header ?>
<?= $sidebar ?>

<div class="col-md-6">
  <div class="how-to">
    <h2>Code Verification (USER Registration)</h2>
      <p>
        This is the second step of the registration process. You can use this method to change the status of the device. 
      </p>
      <p class="note">
        There is no way that you can go through this method if the first step of the process is not completed.
      </p>
      
      <h3>URL</h3>
      &nbsp;
      <?=$url ?>/login/codeVerification
           
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
              <td>phone</td>
              <td><strong>yes</strong></td>
              <td>Phone Number</td>
            </tr>
            <tr>
              <td>deviceId</td>
              <td><strong>yes</strong></td>
              <td>Device unique Identifier. UDID (IOS), IMEI(Android)</td>
            </tr>
            <tr>
              <td>secret</td>
              <td><strong>yes</strong></td>
              <td>Phone secret Key</td>
            </tr>
            <tr>
              <td>code</td>
              <td><strong>yes</strong></td>
              <td>Received SMS code</td>
            </tr>                   
          </tbody>
        </table>
      </div>            
      <h2>Example of a complete request</h2>
      <p>
        This is a working example. Run it from a shell script or with your preferred REST client :-)
      </p>
      <pre>curl -X POST 
        -d phone=666666666
        -d deviceId=1234567
        -d secret=16fbc40beef3403894745379d09d3f7316fb5818
        -d code=fFM5

        <?=$url ?>/login/codeVerification
      <h3>Response</h3>
      <h4>
        If the device is successfully activated:
      </h4>
      <pre>
        Status Code: 200 OK

        {
          "result": "OK"          
        }
      </pre>
      <h4>
        If invalid number of params
      </h4>
      <pre>
        Status Code: 400 Bad Request

      {
        "error": {
          "code": 1000,
          "description": "Invalid Number of Params"
        }
      }
      </pre>
      <h4>
        If the code is not correct:
      </h4>
      <pre>
        Status Code: 400 Bad Request
        
      {
        "error": {
          "code": 4012,
          "description": "Code verification error"
        }
      }
      </pre>

  </div>
</div>
