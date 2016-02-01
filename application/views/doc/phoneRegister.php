
<?= $header ?>
<?= $sidebar ?>

<div class="col-md-6">
  <div class="how-to">
    <h2>Register User Phone (USER Registration)</h2>
      <p>
        This is the first step of the registration process. You can use this method to create a device. 
      </p>
      <p>
        NO user will be assigned to the device until the registration process is finished. After the device is created succesfully 
        a SMS will be sent to the indicated phone.
      </p>
      <p class="note">
        A secret key will be returned that the user device should save for future requests.
      </p>
      <p>
        In case the device already exists for the indicated phone, a new SMS will be sent and a new secret key will be generated.
      </p>

      <h3>URL</h3>
      &nbsp;
      <?=$url ?>/login/phoneRegister
           
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
              <td>type</td>
              <td><strong>yes</strong></td>
              <td>0 -> Android , 1 -> IOS</td>
            </tr>
            <tr>
              <td>screenType</td>
              <td><strong>yes</strong></td>
              <td>0 -> small , 1 -> big</td>
            </tr>  
            <tr>
              <td>vendor</td>
              <td><strong>yes</strong></td>
              <td>Device Vendor</td>
            </tr>  
            <tr>
              <td>model</td>
              <td><strong>yes</strong></td>
              <td>Device model</td>
            </tr>  
            <tr>
              <td>operatingSystem</td>
              <td><strong>yes</strong></td>
              <td>IOS Version or SDK Number</td>
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
        -d type=0
        -d screenType=0
        -d vendor=Samsung
        -d model=SIII
        -d operatingSystem=17
        <?=$url ?>/login/phoneRegister
      <h3>Response</h3>
      <h4>
        If the device is created successfully:
      </h4>
      <pre>
        Status Code: 200 OK

        {
          "device": {
            "id": null,
            "userId": null,
            "deviceId": "654321",
            "secret": "16fbc40beef3403894745379d09d3f7316fb5818",
            "regId": null,
            "deviceToken": null,
            "type": "0",
            "screenType": "0",
            "vendor": "Samsung",
            "model": "SIII",
            "operatingSystem": "17",
            "isLoggedIn": null,
            "verificationCode": "",
            "status": 0,
            "phone": "666666666"
          }
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
        If there is any problem saving the device
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

  </div>
</div>
