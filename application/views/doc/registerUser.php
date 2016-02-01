
<?= $header ?>
<?= $sidebar ?>

<div class="col-md-6">
  <div class="how-to">
    <h2>User Registration (USER Registration)</h2>
      <p>
        This is the last step of the registration process. You can use this method to create the user and associate it to a device. 
      </p>      
      <p class="note">
        If everything goes fine, a session will be created and a TOKEN will be sent.
      </p>
      <p>
        This session Token has a expire Date, so if any time you receive "Invalid Access Token (3000)", you will have to request a new one.
      </p>

      <h3>URL</h3>
      &nbsp;
      <?=$url ?>/login/registerUser
           
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
              <td>alias</td>
              <td><strong>yes</strong></td>
              <td>Wikking User Name</td>
            </tr>  
            <tr>
              <td>winkkinId</td>
              <td><strong>yes</strong></td>
              <td>Unique Id for Wikking</td>
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
        -d alias=sirJimbo
        -d wikkingId=jaime.banus        
        <?=$url ?>/login/registerUser
      <h3>Response</h3>
      <h4>
        If the user is created successfully:
      </h4>
      <pre>
        Status Code: 200 OK

        {
          "token": "e3ca0f6b29a4fc816c3fc1fa3b3f8f56f7b8bf54"          
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
        If there is any problem saving data
      </h4>
      <pre>
        Status Code: 400 Bad Request

      {
       "error": {
          "code": 2000,
          "description": "Error Saving Data"
        }
      }
      </pre>
      <h4>
        If the Wikking ID is already used
      </h4>
      <pre>
        Status Code: 400 Bad Request

      {
        "error": {
          "code": 4006,
          "description": "User Wikking Id already used"
        }
      }
      </pre>
      <h4>
        If the device does not exist or incorrect secret
      </h4>
      <pre>
        Status Code: 400 Bad Request
        
      {
        "error": {
          "code": 4013,
          "description": "Device not registered"
        }
      }
      </pre>

  </div>
</div>
