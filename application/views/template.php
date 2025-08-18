<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (isset($header)) {
    echo $header;
}

if (isset($sidebar)) {
    echo $sidebar;
}

if (isset($content)) {
    echo $content;
}

if (isset($footer)) {
    echo $footer;
}
?>

</div> <!-- fermeture du .wrapper ouvert dans header.php -->
</body>
</html>
