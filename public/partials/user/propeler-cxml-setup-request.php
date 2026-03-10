<?php
    if ( ! defined( 'ABSPATH' ) ) exit;
    echo '<?xml version="1.0"?>' . "\n";
?>
<!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.019/cXML.dtd">
<cXML payloadID="<?php echo esc_attr($data->sessionId); ?>" timestamp="<?php echo esc_attr($data->timestamp); ?>">
    <Response>
        <Status code="200" text="OK"/>
        <PunchOutSetupResponse>
            <StartPage>
                <URL><![CDATA[<?php echo esc_url_raw($data->startUrl); ?>]]></URL>
            </StartPage>
        </PunchOutSetupResponse>
    </Response>
</cXML>