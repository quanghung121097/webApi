define({ "api": [
  {
    "type": "get",
    "url": "/api/auth/user-profile",
    "title": "Lấy thông tin user đăng nhập",
    "name": "Lấy_thông_tin_user_đăng_nhập",
    "group": "Authentication",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Content-Type",
            "description": "<p>application/json</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer Token</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "boolean",
            "optional": false,
            "field": "success",
            "description": "<p>Trạng thái (Thành công hoặc thất bại).</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Nội dung thông báo (nếu có lỗi thì thông báo lỗi).</p>"
          },
          {
            "group": "Success 200",
            "type": "object",
            "optional": false,
            "field": "data",
            "description": "<p>Dữ liệu trả về.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response",
          "content": "HTTP/1.1 200 OK\n{\n     \"success\": true,\n     \"data\": {\n         \"id\": 19\n         \"username\": \"quanghung4\",\n         \"role\": \"customer\",\n         \"created_at\": \"2021-12-08T16:17:59.000000Z\",\n         \"updated_at\": \"2021-12-08T16:17:59.000000Z\"\n     }\n}",
          "type": "json"
        }
      ]
    },
    "sampleRequest": [
      {
        "url": "https://qhshop.xyz/api/auth/user-profile"
      }
    ],
    "examples": [
      {
        "title": "Curl",
        "content": "curl --location --request GET \"https://qhshop.xyz/api/auth/user-profile\" \\\n    -H  'Content-Type: application/json' \\\n    -H  'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY' \\",
        "type": "json"
      },
      {
        "title": "Node.js",
        "content": "const axios = require('axios');\ntry {\nconst response = await axios({\n  method: 'GET',\n  url: 'https://qhshop.xyz/api/auth/user-profile',\n  headers: {\n     'Content-Type': 'application/json',\n     'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'\n  },\n});\nconsole.log(response);\n} catch (error) {\nconsole.error(error);\n}",
        "type": "json"
      },
      {
        "title": "PHP",
        "content": "<?php\n//Sử dụng pecl_http\n$client = new http\\Client;\n$request = new http\\Client\\Request;\n$request->setRequestUrl('https://qhshop.xyz/api/auth/user-profile');\n$request->setRequestMethod('GET');\n$body = new http\\Message\\Body;\n$body->append('{}');\n$request->setBody($body);\n$request->setOptions(array());\n$request->setHeaders(array(\n'Content-Type' => 'application/json',\n'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'\n));\n$client->enqueue($request)->send();\n$response = $client->getResponse();\necho $response->getBody();",
        "type": "json"
      },
      {
        "title": "Python:",
        "content": "import http.client\nimport mimetypes\nconn = http.client.HTTPSConnection(\"https://qhshop.xyz\")\npayload = '{}'\nconn.request(\"GET\", \"/api/auth/user-profile\", payload, headers);\nres = conn.getresponse()\ndata = res.read()\nprint(data.decode(\"utf-8\"))",
        "type": "json"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response",
          "content": "\nHTTP/1.1 401 Unauthorized\n{\n\"success\": false,\n\"message\": \"Unauthenticated.\"\n}\nHTTP/1.1 500 Internal Server Error\n{\n\"success\": false,\n\"message\": \"Có lỗi xảy ra. Vui lòng thử lại sau!\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "1.0.0",
    "filename": "app/Http/Controllers/Api/AuthController.php",
    "groupTitle": "Authentication"
  },
  {
    "type": "post",
    "url": "/api/auth/register",
    "title": "Đăng ký tài khoản (khách hàng)",
    "name": "Đăng_ký_tài_khoản",
    "group": "Authentication",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Content-Type",
            "description": "<p>application/json</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Body": [
          {
            "group": "Body",
            "type": "String",
            "optional": false,
            "field": "username",
            "description": "<p>Tên tài khoản dùng để đăng nhập (bắt buộc,5 ký tự trở lên, duy nhất).</p>"
          },
          {
            "group": "Body",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>Mật khẩu tài khoản (ít nhất 8 ký tự, gồm 1 ký tự viết hoa, 1 ký tự viết thường, 1 số).</p>"
          },
          {
            "group": "Body",
            "type": "String",
            "optional": false,
            "field": "confirm_password",
            "description": "<p>Xác nhận lại mật khẩu đăng ký (bắt buộc).</p>"
          },
          {
            "group": "Body",
            "type": "String",
            "optional": false,
            "field": "phone",
            "description": "<p>Số điện thoại (bắt buộc).</p>"
          },
          {
            "group": "Body",
            "type": "String",
            "optional": false,
            "field": "gender",
            "description": "<p>Giới tính (1 nam, 0 nữ, mặc định là 1).</p>"
          },
          {
            "group": "Body",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>Địa chỉ email (bắt buộc, đúng định dạng email, duy nhất).</p>"
          },
          {
            "group": "Body",
            "type": "String",
            "optional": false,
            "field": "full_name",
            "description": "<p>Họ và tên (bắt buộc, tối đa 40 ký tự).</p>"
          },
          {
            "group": "Body",
            "type": "String",
            "optional": false,
            "field": "address",
            "description": "<p>Địa chỉ (bắt buộc),</p>"
          },
          {
            "group": "Body",
            "type": "String",
            "optional": false,
            "field": "dateOfBirth",
            "description": "<p>(bắt buộc, định dạng Y-m-d).</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "boolean",
            "optional": false,
            "field": "success",
            "description": "<p>Trạng thái (Thành công hoặc thất bại).</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Nội dung thông báo (nếu có lỗi thì thông báo lỗi).</p>"
          },
          {
            "group": "Success 200",
            "type": "object",
            "optional": false,
            "field": "data",
            "description": "<p>Dữ liệu trả về.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response",
          "content": "HTTP/1.1 200 OK\n{\n     \"success\": true,\n     \"message\": \"Đã đăng ký thành công mời bạn đăng nhập\",\n     \"person\": {\n         \"account_id\": 19,\n         \"full_name\": \"Nguyễn Văn Quang Hưng\",\n         \"gender\": \"Nam\",\n         \"address\": \"Hải Phòng\",\n         \"date_of_birth\": \"1997-10-11T17:00:00.000000Z\",\n         \"phone\": \"0788337682\",\n         \"email\": \"quanghung1210971@gmail.com\",\n         \"updated_at\": \"2021-12-08T16:17:59.000000Z\",\n         \"created_at\": \"2021-12-08T16:17:59.000000Z\",\n         \"id\": 12\n     }\n}",
          "type": "json"
        }
      ]
    },
    "sampleRequest": [
      {
        "url": "https://qhshop.xyz/api/auth/register"
      }
    ],
    "body": [
      {
        "group": "Body",
        "type": "String",
        "optional": true,
        "field": "username",
        "defaultValue": "quanghung4",
        "description": ""
      },
      {
        "group": "Body",
        "type": "String",
        "optional": true,
        "field": "password",
        "defaultValue": "Quanghung1210",
        "description": ""
      },
      {
        "group": "Body",
        "type": "String",
        "optional": true,
        "field": "confirm_password",
        "defaultValue": "Quanghung1210",
        "description": ""
      },
      {
        "group": "Body",
        "type": "String",
        "optional": true,
        "field": "phone",
        "defaultValue": "0788337682",
        "description": ""
      },
      {
        "group": "Body",
        "type": "String",
        "optional": true,
        "field": "gender",
        "defaultValue": "1",
        "description": ""
      },
      {
        "group": "Body",
        "type": "String",
        "optional": true,
        "field": "email",
        "defaultValue": "quanghung121097@gmail.com",
        "description": ""
      },
      {
        "group": "Body",
        "type": "String",
        "optional": true,
        "field": "full_name",
        "defaultValue": "Nguyễn Văn Quang Hưng",
        "description": ""
      },
      {
        "group": "Body",
        "type": "String",
        "optional": true,
        "field": "address",
        "defaultValue": "Hải Phòng",
        "description": ""
      },
      {
        "group": "Body",
        "type": "String",
        "optional": true,
        "field": "dateOfBirth",
        "defaultValue": "1997-10-12",
        "description": ""
      }
    ],
    "examples": [
      {
        "title": "Curl",
        "content": "curl --location --request POST \"https://qhshop.xyz/api/auth/register\" \\\n    -H  'Content-Type: application/json' \\\n    -d '{\n           \"username\": \"quanghung4\",\n           \"password\": \"Quanghung1210\",\n           \"confirm_password\": \"Quanghung1210\",\n           \"full_name\": \"Nguyễn Văn Quang Hưng\",\n           \"gender\" : 1,\n           \"address\" : \"Hải Phòng\",\n           \"dateOfBirth\": \"1997-10-12\",\n           \"phone\" : \"0788337682\",\n           \"email\" : \"quanghung121097@gmail.com\"\n     }'",
        "type": "json"
      },
      {
        "title": "Node.js",
        "content": "const axios = require('axios');\ntry {\nconst response = await axios({\n  method: 'POST',\n  url: 'https://qhshop.xyz/api/auth/register',\n  headers: {\n     'Content-Type': 'application/json'\n  },\n  data: {\n            \"username\": \"quanghung4\",\n            \"password\": \"Quanghung1210\",\n            \"confirm_password\": \"Quanghung1210\",\n            \"full_name\": \"Nguyễn Văn Quang Hưng\",\n            \"gender\" : 1,\n            \"address\" : \"Hải Phòng\",\n            \"dateOfBirth\": \"1997-10-12\",\n            \"phone\" : \"0788337682\",\n            \"email\" : \"quanghung121097@gmail.com\"\n }\n});\nconsole.log(response);\n} catch (error) {\nconsole.error(error);\n}",
        "type": "json"
      },
      {
        "title": "PHP",
        "content": "<?php\n//Sử dụng pecl_http\n$client = new http\\Client;\n$request = new http\\Client\\Request;\n$request->setRequestUrl('https://qhshop.xyz/api/auth/register');\n$request->setRequestMethod('POST');\n$body = new http\\Message\\Body;\n$body->append('{\n            \"username\": \"quanghung4\",\n            \"password\": \"Quanghung1210\",\n            \"confirm_password\": \"Quanghung1210\",\n            \"full_name\": \"Nguyễn Văn Quang Hưng\",\n            \"gender\" : 1,\n            \"address\" : \"Hải Phòng\",\n            \"dateOfBirth\": \"1997-10-12\",\n            \"phone\" : \"0788337682\",\n            \"email\" : \"quanghung121097@gmail.com\"\n}');\n$request->setBody($body);\n$request->setOptions(array());\n$request->setHeaders(array(\n'Content-Type' => 'application/json'\n));\n$client->enqueue($request)->send();\n$response = $client->getResponse();\necho $response->getBody();",
        "type": "json"
      },
      {
        "title": "Python:",
        "content": "import http.client\nimport mimetypes\nconn = http.client.HTTPSConnection(\"https://qhshop.xyz\")\npayload = '{\n            \"username\": \"quanghung4\",\n            \"password\": \"Quanghung1210\",\n            \"confirm_password\": \"Quanghung1210\",\n            \"full_name\": \"Nguyễn Văn Quang Hưng\",\n            \"gender\" : 1,\n            \"address\" : \"Hải Phòng\",\n            \"dateOfBirth\": \"1997-10-12\",\n            \"phone\" : \"0788337682\",\n            \"email\" : \"quanghung121097@gmail.com\"\n}'\nconn.request(\"POST\", \"/api/auth/register\", payload, headers);\nres = conn.getresponse()\ndata = res.read()\nprint(data.decode(\"utf-8\"))",
        "type": "json"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response",
          "content": "\nHTTP/1.1 400 Bad Request\n{\n\"success\": false,\n\"message\": {\n     \"username\": [\n         \"Tên tài khoản là bắt buộc.\"\n     ],\n     \"password\": [\n         \"Mật khẩu tối thiểu tám ký tự, ít nhất một chữ cái viết hoa, một chữ cái viết thường và một số\"\n     ],\n     ...\n }\n}\nHTTP/1.1 500 Internal Server Error\n{\n\"success\": false,\n\"message\": \"Có lỗi xảy ra. Vui lòng thử lại sau!\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "1.0.0",
    "filename": "app/Http/Controllers/Api/AuthController.php",
    "groupTitle": "Authentication"
  },
  {
    "type": "post",
    "url": "/api/auth/login",
    "title": "Đăng nhập tài khoản",
    "name": "Đăng_nhập_tài_khoản",
    "group": "Authentication",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Content-Type",
            "description": "<p>application/json</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Body": [
          {
            "group": "Body",
            "type": "String",
            "optional": false,
            "field": "username",
            "description": "<p>Tài khoản của người dùng.</p>"
          },
          {
            "group": "Body",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>Mật khẩu tài khoản người dùng.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "boolean",
            "optional": false,
            "field": "success",
            "description": "<p>Trạng thái (Thành công hoặc thất bại).</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Nội dung thông báo (nếu có lỗi thì thông báo lỗi).</p>"
          },
          {
            "group": "Success 200",
            "type": "object",
            "optional": false,
            "field": "data",
            "description": "<p>Dữ liệu trả về.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response",
          "content": "HTTP/1.1 200 OK\n{\n\"success\": true,\n\"data\": {\n  \"token\": \"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk3NTMwMywiZXhwIjoxNjM4OTc4OTAzLCJuYmYiOjE2Mzg5NzUzMDMsImp0aSI6IlNxT0ZBcjhpV25VdWhscEIiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.CV7hGvuDLMG_PiC4exfWeLLdYlIhDJg117ZILtPl5tU\",\n  \"user\": {\n     \"id\": 18,\n      \"username\": \"quanghung1\",\n     \"role\": \"customer\",\n     \"created_at\": \"2021-12-05T11:18:11.000000Z\",\n    \"updated_at\": \"2021-12-05T16:55:23.000000Z\"\n}\n}\n}",
          "type": "json"
        }
      ]
    },
    "sampleRequest": [
      {
        "url": "https://qhshop.xyz/api/auth/login"
      }
    ],
    "body": [
      {
        "group": "Body",
        "type": "String",
        "optional": true,
        "field": "username",
        "defaultValue": "quanghung1",
        "description": ""
      },
      {
        "group": "Body",
        "type": "String",
        "optional": true,
        "field": "password",
        "defaultValue": "Quanghung1210",
        "description": ""
      }
    ],
    "examples": [
      {
        "title": "Curl",
        "content": "curl --location --request POST \"https://qhshop.xyz/api/auth/login\" \\\n    -H  'Content-Type: application/json' \\\n    -d '{\n      \"username\":\"quanghung1\",\n      \"password\":\"Quanghung1210\"\n     }'",
        "type": "json"
      },
      {
        "title": "Node.js",
        "content": "const axios = require('axios');\ntry {\nconst response = await axios({\n  method: 'POST',\n  url: 'https://qhshop.xyz/api/auth/login',\n  headers: {\n     'Content-Type': 'application/json'\n  },\n  data: {\n     \"username\":\"quanghung1\",\n     \"password\":\"Quanghung1210\"\n }\n});\nconsole.log(response);\n} catch (error) {\nconsole.error(error);\n}",
        "type": "json"
      },
      {
        "title": "PHP",
        "content": "<?php\n//Sử dụng pecl_http\n$client = new http\\Client;\n$request = new http\\Client\\Request;\n$request->setRequestUrl('https://qhshop.xyz/api/auth/login');\n$request->setRequestMethod('POST');\n$body = new http\\Message\\Body;\n$body->append('{\n     \"username\":\"quanghung1\",\n     \"password\":\"Quanghung1210\"\n}');\n$request->setBody($body);\n$request->setOptions(array());\n$request->setHeaders(array(\n'Content-Type' => 'application/json'\n));\n$client->enqueue($request)->send();\n$response = $client->getResponse();\necho $response->getBody();",
        "type": "json"
      },
      {
        "title": "Python:",
        "content": "import http.client\nimport mimetypes\nconn = http.client.HTTPSConnection(\"https://qhshop.xyz\")\npayload = '{\n     \"username\":\"quanghung1\",\n       \"password\":\"Quanghung1210\"\n}'\nconn.request(\"POST\", \"/api/auth/login\", payload, headers);\nres = conn.getresponse()\ndata = res.read()\nprint(data.decode(\"utf-8\"))",
        "type": "json"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response",
          "content": "HTTP/1.1 422 Unprocessable Entity\n{\n \"success\": false,\n \"message\": \"Sai thông tin đăng nhập\"\n}\n\nHTTP/1.1 400 Bad Request\n{\n\"success\": false,\n\"message\": {\n     \"username\": [\n         \"Tên tài khoản là bắt buộc.\"\n     ],\n     \"password\": [\n         \"Mật khẩu là bắt buộc.\"\n     ]\n }\n}\nHTTP/1.1 500 Internal Server Error\n{\n\"success\": false,\n\"message\": \"Có lỗi xảy ra. Vui lòng thử lại sau!\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "1.0.0",
    "filename": "app/Http/Controllers/Api/AuthController.php",
    "groupTitle": "Authentication"
  },
  {
    "type": "post",
    "url": "/api/auth/logout",
    "title": "Đăng xuất tài khoản",
    "name": "Đăng_xuất_tài_khoản",
    "group": "Authentication",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Content-Type",
            "description": "<p>application/json</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer Token</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "boolean",
            "optional": false,
            "field": "success",
            "description": "<p>Trạng thái (Thành công hoặc thất bại).</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Nội dung thông báo (nếu có lỗi thì thông báo lỗi).</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response",
          "content": "HTTP/1.1 200 OK\n{\n     \"success\": true,\n     \"message\": \"Đăng xuất thành công\"\n}",
          "type": "json"
        }
      ]
    },
    "sampleRequest": [
      {
        "url": "https://qhshop.xyz/api/auth/logout"
      }
    ],
    "examples": [
      {
        "title": "Curl",
        "content": "curl --location --request POST \"https://qhshop.xyz/api/auth/logout\" \\\n    -H  'Content-Type: application/json' \\\n    -H  'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'",
        "type": "json"
      },
      {
        "title": "Node.js",
        "content": "const axios = require('axios');\ntry {\nconst response = await axios({\n  method: 'POST',\n  url: 'https://qhshop.xyz/api/auth/logout',\n  headers: {\n     'Content-Type': 'application/json',\n     'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'\n  },\n});\nconsole.log(response);\n} catch (error) {\nconsole.error(error);\n}",
        "type": "json"
      },
      {
        "title": "PHP",
        "content": "<?php\n//Sử dụng pecl_http\n$client = new http\\Client;\n$request = new http\\Client\\Request;\n$request->setRequestUrl('https://qhshop.xyz/api/auth/logout');\n$request->setRequestMethod('POST');\n$body = new http\\Message\\Body;\n$body->append('{}');\n$request->setBody($body);\n$request->setOptions(array());\n$request->setHeaders(array(\n'Content-Type' => 'application/json',\n'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'\n));\n$client->enqueue($request)->send();\n$response = $client->getResponse();\necho $response->getBody();",
        "type": "json"
      },
      {
        "title": "Python:",
        "content": "import http.client\nimport mimetypes\nconn = http.client.HTTPSConnection(\"https://qhshop.xyz\")\npayload = '{}'\nconn.request(\"POST\", \"/api/auth/logout\", payload, headers);\nres = conn.getresponse()\ndata = res.read()\nprint(data.decode(\"utf-8\"))",
        "type": "json"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response",
          "content": "\nHTTP/1.1 401 Unauthorized\n{\n\"success\": false,\n\"message\": \"Unauthenticated.\"\n}\nHTTP/1.1 500 Internal Server Error\n{\n\"success\": false,\n\"message\": \"Có lỗi xảy ra. Vui lòng thử lại sau!\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "1.0.0",
    "filename": "app/Http/Controllers/Api/AuthController.php",
    "groupTitle": "Authentication"
  },
  {
    "type": "post",
    "url": "/api/auth/change-password",
    "title": "Đổi mật khẩu user",
    "name": "Đổi_mật_khẩu_user",
    "group": "Authentication",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Content-Type",
            "description": "<p>application/json</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer Token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Body": [
          {
            "group": "Body",
            "type": "String",
            "optional": false,
            "field": "old_password",
            "description": "<p>Mật khẩu cũ (bắt buộc).</p>"
          },
          {
            "group": "Body",
            "type": "String",
            "optional": false,
            "field": "new_password",
            "description": "<p>Mật khẩu mới (ít nhất 8 ký tự, gồm 1 ký tự viết hoa, 1 ký tự viết thường, 1 số).</p>"
          },
          {
            "group": "Body",
            "type": "String",
            "optional": false,
            "field": "new_password_confirmation",
            "description": "<p>Xác nhận lại mật khẩu đăng ký (bắt buộc).</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "boolean",
            "optional": false,
            "field": "success",
            "description": "<p>Trạng thái (Thành công hoặc thất bại).</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Nội dung thông báo (nếu có lỗi thì thông báo lỗi).</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response",
          "content": "HTTP/1.1 200 OK\n{\n     \"success\": true,\n     \"message\": \"Thay đổi mật khẩu thành công\",\n}",
          "type": "json"
        }
      ]
    },
    "sampleRequest": [
      {
        "url": "https://qhshop.xyz/api/auth/change-password"
      }
    ],
    "body": [
      {
        "group": "Body",
        "type": "String",
        "optional": true,
        "field": "old_password",
        "defaultValue": "Quanghung1210",
        "description": ""
      },
      {
        "group": "Body",
        "type": "String",
        "optional": true,
        "field": "new_password",
        "defaultValue": "Quanghung121097",
        "description": ""
      },
      {
        "group": "Body",
        "type": "String",
        "optional": true,
        "field": "new_password_confirmation",
        "defaultValue": "Quanghung121097",
        "description": ""
      }
    ],
    "examples": [
      {
        "title": "Curl",
        "content": "curl --location --request POST \"https://qhshop.xyz/api/auth/change-password\" \\\n    -H  'Content-Type: application/json' \\\n    -H  'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY' \\\n    -d '{\n           \"old_password\": \"Quanghung1210\",\n           \"new_password\": \"Quanghung121097\",\n           \"new_password_confirmation\": \"Quanghung121097\"\n     }'",
        "type": "json"
      },
      {
        "title": "Node.js",
        "content": "const axios = require('axios');\ntry {\nconst response = await axios({\n  method: 'POST',\n  url: 'https://qhshop.xyz/api/auth/change-password',\n  headers: {\n     'Content-Type': 'application/json',\n     'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'\n  },\n  data: {\n            \"old_password\": \"Quanghung1210\",\n            \"new_password\": \"Quanghung121097\",\n            \"new_password_confirmation\": \"Quanghung121097\"\n }\n});\nconsole.log(response);\n} catch (error) {\nconsole.error(error);\n}",
        "type": "json"
      },
      {
        "title": "PHP",
        "content": "<?php\n//Sử dụng pecl_http\n$client = new http\\Client;\n$request = new http\\Client\\Request;\n$request->setRequestUrl('https://qhshop.xyz/api/auth/change-password');\n$request->setRequestMethod('POST');\n$body = new http\\Message\\Body;\n$body->append('{\n            \"old_password\": \"Quanghung1210\",\n            \"new_password\": \"Quanghung121097\",\n            \"new_password_confirmation\": \"Quanghung121097\"\n}');\n$request->setBody($body);\n$request->setOptions(array());\n$request->setHeaders(array(\n'Content-Type' => 'application/json',\n'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'\n));\n$client->enqueue($request)->send();\n$response = $client->getResponse();\necho $response->getBody();",
        "type": "json"
      },
      {
        "title": "Python:",
        "content": "import http.client\nimport mimetypes\nconn = http.client.HTTPSConnection(\"https://qhshop.xyz\")\npayload = '{\n            \"old_password\": \"Quanghung1210\",\n            \"new_password\": \"Quanghung121097\",\n            \"new_password_confirmation\": \"Quanghung121097\"\n}'\nconn.request(\"POST\", \"/api/auth/change-password\", payload, headers);\nres = conn.getresponse()\ndata = res.read()\nprint(data.decode(\"utf-8\"))",
        "type": "json"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response",
          "content": "\nHTTP/1.1 400 Bad Request\n{\n\"success\": false,\n\"message\": {\n     \"old_password\": [\n         \"Mật khẩu cũ là bắt buộc.\"\n     ],\n     \"new_password\": [\n         \"Mật khẩu tối thiểu tám ký tự, ít nhất một chữ cái viết hoa, một chữ cái viết thường và một số\"\n     ],\n     \"new_password_confirmation\": [\n         \"Mật khẩu xác nhận không khớp\"\n     ]\n }\n}\nHTTP/1.1 500 Internal Server Error\n{\n\"success\": false,\n\"message\": \"Có lỗi xảy ra. Vui lòng thử lại sau!\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "1.0.0",
    "filename": "app/Http/Controllers/Api/AuthController.php",
    "groupTitle": "Authentication"
  },
  {
    "type": "DELETE",
    "url": "/api/product/delete",
    "title": "Xóa sản phẩm",
    "name": "Xóa_sản_phẩm",
    "group": "Product",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Content-Type",
            "description": "<p>application/json</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer Token</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "boolean",
            "optional": false,
            "field": "success",
            "description": "<p>Trạng thái (Thành công hoặc thất bại).</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Nội dung thông báo (nếu có lỗi thì thông báo lỗi).</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response",
          "content": "HTTP/1.1 200 OK\n{\n     \"success\": true,\n     \"message\": \"Xóa sản phẩm thành công\"\n}",
          "type": "json"
        }
      ]
    },
    "sampleRequest": [
      {
        "url": "https://qhshop.xyz/api/auth/delete"
      }
    ],
    "body": [
      {
        "group": "Body",
        "type": "String",
        "optional": true,
        "field": "id",
        "defaultValue": "6",
        "description": ""
      }
    ],
    "examples": [
      {
        "title": "Curl",
        "content": "curl --location --request DELETE \"https://qhshop.xyz/api/auth/delete\" \\\n    -H  'Content-Type: application/json' \\\n    -H  'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY' \\\n    -d '{\n           \"id\": 6\n     }'",
        "type": "json"
      },
      {
        "title": "Node.js",
        "content": "const axios = require('axios');\ntry {\nconst response = await axios({\n  method: 'DELETE',\n  url: 'https://qhshop.xyz/api/auth/delete',\n  headers: {\n     'Content-Type': 'application/json',\n     'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'\n  },\n  data: {\n            \"id\": 6\n }\n});\nconsole.log(response);\n} catch (error) {\nconsole.error(error);\n}",
        "type": "json"
      },
      {
        "title": "PHP",
        "content": "<?php\n//Sử dụng pecl_http\n$client = new http\\Client;\n$request = new http\\Client\\Request;\n$request->setRequestUrl('https://qhshop.xyz/api/auth/delete');\n$request->setRequestMethod('DELETE');\n$body = new http\\Message\\Body;\n$body->append('{\n            \"id\": 6\n}');\n$request->setBody($body);\n$request->setOptions(array());\n$request->setHeaders(array(\n'Content-Type' => 'application/json',\n'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'\n));\n$client->enqueue($request)->send();\n$response = $client->getResponse();\necho $response->getBody();",
        "type": "json"
      },
      {
        "title": "Python:",
        "content": "import http.client\nimport mimetypes\nconn = http.client.HTTPSConnection(\"https://qhshop.xyz\")\npayload = '{\n            \"id\": 6\n}'\nconn.request(\"DELETE\", \"/api/auth/delete\", payload, headers);\nres = conn.getresponse()\ndata = res.read()\nprint(data.decode(\"utf-8\"))",
        "type": "json"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response",
          "content": "\nHTTP/1.1 401 Unauthorized\n{\n\"success\": false,\n\"message\": \"Unauthenticated.\"\n}\n\n*\nHTTP/1.1 401 Unauthorized\n{\n\"success\": false,\n\"message\": \"Tài khoản không có quyền thực hiện hành động này.\"\n}\n\nHTTP/1.1 400 Bad Request\n{\n\"success\": false,\n\"message\": \"Thiếu id sản phẩm\"\n}\n\nHTTP/1.1 404 Not Found\n{\n\"success\": false,\n\"message\": \"Không tìm thấy sản phẩm\"\n}\nHTTP/1.1 500 Internal Server Error\n{\n\"success\": false,\n\"message\": \"Có lỗi xảy ra. Vui lòng thử lại sau!\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "1.0.0",
    "filename": "app/Http/Controllers/Api/ApiProductController.php",
    "groupTitle": "Product"
  }
] });