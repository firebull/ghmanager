<?php


/*
 * Created on 16.01.2012
 */
?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://client.ishop.mw.ru/">
	<SOAP-ENV:Body>
		<ns1:updateBillResponse>
			<updateBillResult><?php print_r($status); ?></updateBillResult>
		</ns1:updateBillResponse>
	</SOAP-ENV:Body>
</SOAP-ENV:Envelope>
