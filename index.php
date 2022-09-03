<?php

include_once 'controllers/paymentController.php';
$get = (object)$_GET;
//echo json_encode($get);
$params=(new paymentController())->initiate($get);

?>
<html>
<body>

<form id="cybersource_pay" action="<?= PAYMENT_URL ?>" method="post"/>
<?php
foreach ($params as $name => $value) {
    echo "<input type=\"hidden\" id=\"" . $name . "\" name=\"" . $name . "\" value=\"" . $value . "\"/>\n";
}
?>
</form>
<script
        src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ="
        crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {
        $("form#cybersource_pay").submit();
    });
</script>


</body>
</html>



