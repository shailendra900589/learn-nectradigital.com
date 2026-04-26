<?php
require_once 'includes/db.php';
header('Content-Type: application/opensearchdescription+xml; charset=UTF-8');
$site_url = rtrim(site_base_url(), '/');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
    <ShortName>Learn.Nectra</ShortName>
    <Description>Search Learn.Nectra programming tutorials and courses.</Description>
    <InputEncoding>UTF-8</InputEncoding>
    <Image width="16" height="16" type="image/x-icon"><?php echo h($site_url); ?>/favicon.ico</Image>
    <Url type="text/html" method="get" template="<?php echo h($site_url); ?>/search?q={searchTerms}" />
</OpenSearchDescription>
