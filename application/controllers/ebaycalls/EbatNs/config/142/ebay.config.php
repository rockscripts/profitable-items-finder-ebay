<?php /*

[ebay-config]

; place keys and certificate here as provided from eBay

; this is the tripple for the sandbox
compat-level = 533
dev-key-test = f579acc9-abc6-4e3b-bd27-8f9bd868b664

app-key-test = rockscri-de85-43d0-a7ac-b26221849977

cert-id-test = 48cdea4b-f1d1-4655-b474-2bc16b9f0ef5

; and here for the production environment once you passed certification

dev-key-prod  = f579acc9-abc6-4e3b-bd27-8f9bd868b664

app-key-prod  = rockscri-7a27-4c7a-b095-e8cbe5ebb63b

cert-id-prod =  1a6a02c2-9eba-4725-aafa-f1b2d519b678



; primary site id

site-id = 0



; 1 => sandbox, 0 => production

app-mode = 0

;app-mode = 1


[ebay-transaction-config]

use-http-compression=0
[token]
; put in here the full absolute path to your config file !
;local
;token-pickup-file=/home/alex/public_html/hosting/EbatNs/samples/config/my.token
;server
token-pickup-file=/home/rocksc5/public_html/designmanager/application/controllers/ebaycalls/EbatNs/config/142/my.token
token-mode=1
*/ ?>