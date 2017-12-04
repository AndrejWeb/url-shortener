<?php

session_start();

require 'app/vendor/autoload.php';

$db = \App\Database\DB::getInstance();
$urlShortener = new \App\URLShortener();

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
    if(isset($_POST['token'], $_POST['id']))
    {
        if($_POST['token'] == $_SESSION['token'])
        {
            $url_id = str_replace('url', '', $_POST['id']);
            $url = $urlShortener->getShortURLByID($url_id);
            ?>
            <div class="url-visits">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php echo $url["code"]; ?></h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr class="bg-info">
                            <th>Visit Timestamp</th>
                        </tr>
                        <?php foreach($urlShortener->getURLVisits($url_id) as $url_visited) { ?>
                            <tr>
                                <td><?php echo $url_visited["created_at"]; ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
<?php
        }
    }
}
