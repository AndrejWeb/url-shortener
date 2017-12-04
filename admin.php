<?php

session_start();

require 'app/vendor/autoload.php';
$urlShortener = new \App\URLShortener();

if(!isset($_SESSION['token']))
{
    $_SESSION['token'] = sha1(uniqid().time());
}

$url_errors = array();

/*
 * Add URLs
 */
if(isset($_POST['urls'], $_POST['token']))
{
    if($_POST['token'] == $_SESSION['token'])
    {
        $urls = explode("\n", $_POST['urls']);
        if(count($urls))
        {
            foreach($urls as $url)
            {
                if($url != '')
                {
                    if (!preg_match("#https?://#", $url))
                    {
                        $url_errors[] = $url;
                    }
                    else
                    {
                        $urlShortener->createShortURL($url);
                    }
                }
            }
        }
    }
}

/*
 * Delete URL
 */
if(isset($_POST['token'], $_POST['_method'], $_POST['action'], $_POST['url']))
{
    if($_POST['token'] == $_SESSION['token'])
    {
        if($_POST['_method'] == 'delete' && $_POST['action'] == 'delete' && is_integer((int)$_POST['url']))
        {
            $urlShortener->deleteURL($_POST['url']);
            $urlShortener->deleteURLVisits($_POST['url']);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>URL Shortener Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<div class="container-fluid">
    <form method="post">
        <div class="row">
            <?php
           if(count($url_errors) > 0) { ?>
           <div class="alert alert-danger">
               The entered URLs below were invalid and not saved. URL must start with http:// or https://
           </div>
           <?php } ?>
            <div class="col-xs-6">
                <textarea name="urls" class="form-control" placeholder="Enter URLs. One URL per line." style="width: 100%;" rows="10"><?php if(count($url_errors) > 0) { foreach($url_errors as $url) echo $url; } ?></textarea>
            </div>
            <div class="col-xs-6">
                <input type="submit" class="btn btn-primary btn-lg" value="Submit" />
                <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
            </div>
        </div>
    </form>
    <br />
<hr/>
<a href="admin.php" class="btn btn-success">Refresh</a>
<br /><br />
<table class="table table-hover table-bordered" id="urls-table">
    <thead>
        <tr class="bg-primary">
            <th>ID</th>
            <th>Code</th>
            <th>Shorten URL</th>
            <th>Destination URL</th>
            <th>URL Date Created</th>
            <th>Visits</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
            $urls = $urlShortener->getAllURLs();
            if($urls)
            {
                foreach($urls as $url) { ?>
        <tr>
            <td><?php echo $url["id"]; ?></td>
            <td><?php echo $url["code"]; ?></td>
            <td><a href="http://<?php echo $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']); ?>/<?php echo $url["code"]; ?>" target="_blank"><?php echo "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/" . $url["code"]; ?></a></td>
            <td><a href="<?php echo $url["destination"]; ?>" target="_blank"><?php echo $url["destination"]; ?></a></td>
            <td><?php echo $url["created_at"]; ?></td>
            <td class="text-center">
                <span class="label label-success url-total-visits" id="url<?php echo $url["id"]; ?>"><?php echo $urlShortener->getTotalVisits($url["id"]); ?></span>
            </td>
            <td class="text-center"><img src="images/delete.png" alt="Delete" title="Delete" class="delete-url" id="<?php echo $url["id"]; ?>" /></td>
        </tr>
        <?php
                }
            }
        ?>
    </tbody>
</table>
</div>

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModal" id="modalVisits">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="js/jquery.tablesorter.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        $("#urls-table").tablesorter();

        $(".delete-url").click(function() {
           if(confirm("Are you sure you want to delete this URL?")) {
               var token = "<?php echo $_SESSION['token']; ?>";
               var url_id = $(this).attr("id");
               $('<form method="post"><input type="hidden" name="token" value="'+ token +'" /><input type="hidden" name="url" value="'+url_id+'" /><input type="hidden" name="_method" value="delete" /><input type="hidden" name="action" value="delete" /></form>').appendTo('body').submit();
           }
        });

        $(".url-total-visits").click(function() {
           $.post("get_url_visits.php", { token:"<?php echo $_SESSION['token']; ?>", id:$(this).attr("id") }, function(data) {
               $(".modal-content").html(data);
               $("#modalVisits").modal("show");
           });
        });
    });
</script>
</body>
</html>