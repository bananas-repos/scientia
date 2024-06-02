# Request:

```
POST http://example.tld/api.php
Content-Type: application/json; charset=utf-8
Accept: application/json

{
	"asl": "YOUR-KEY",
	"data": "TEXT DATA TO BE SAVED"
}
```

# Response success:

```
Content-Type: application/json
{
	"message": "http://example.tld/2022/03/26/DFzn",
	"status": 200
}
```

# Response failure:

```
Content-Type: application/json
{
	"message": "Something went wrong. HASHCODE",
	"status": 500
}
```
