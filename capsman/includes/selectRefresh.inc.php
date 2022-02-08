<?php

echo "<div class=\"RefreshSelect\" style=\"float: right;\">";
echo "<a href=\"javascript:void(0)\" onclick=\"refreshSelect('".$_SERVER["PHP_SELF"]."');\">Refresh</a>";
echo "<span>&nbsp;&nbsp;</span>";
echo <<<EOF
<select>
    <option value="10">Every 10 seconds</option>
    <option value="20">Every 20 seconds</option>
    <option value="30">Every 30 seconds</option>
    <option value="60">Every minute</option>
    <option value="120">Every 2 minutes</option>
    <option value="300">Every 5 minutes</option>
</select>
</div>
<div class="spacer">&nbsp;</div>
EOF

?>
