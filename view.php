<?php if(!defined('PROJECT_STATUS')) die('uh, nope!') ?>
<!doctype html> 

<!--[if lt IE 7 ]> <html lang="en" class="no-js ie ie6"> <![endif]--> 
<!--[if IE 7 ]>    <html lang="en" class="no-js ie ie7"> <![endif]--> 
<!--[if IE 8 ]>    <html lang="en" class="no-js ie ie8"> <![endif]--> 
<!--[if IE 9 ]>    <html lang="en" class="no-js ie ie9"> <![endif]--> 
<!--[if gt IE 9]>  <html lang="en" class="no-js ie">     <![endif]--> 
<!--[if !IE]><!--> <html lang="en" class="no-js">    <!--<![endif]--> 
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?php echo $project_status->title() ;?></title>

        <link rel="stylesheet" href="style.css" /> 
        <link rel="author" href="http://voltampmedia.com" />
        <link rel="auther" href="htpp://factor1studios.com" />
    </head>
    <body>

    <div id="msg_container">
    <?php foreach($project_status->get_msgs() as $row) {
        switch ($row['level']) {
            case 'info': ?> <div class="message info-msg"><h5>Information:</h5><?php echo $row['msg']; ?></div> <?php
                break;
            case 'error': ?> <div class="message error-msg"><h5>Error:</h5><?php echo $row['msg']; ?></div> <?php
                break;
            case 'warning': ?> <div class="message warning-msg"><h5>Warning:</h5><?php echo $row['msg']; ?></div> <?php
                break;
            case 'success': ?> <div class="message success-msg"><h5>Success:</h5><?php echo $row['msg']; ?></div> <?php
                break;
        }
    } ?>
    </div>

    <?php if(!$project_status->loggedin()) { ?>
    <span id="click_to_login">click here to login</span>
        <form id="login" method="post" action="?action=login">
            <input type="text" id="username" name="username" value="<?php echo $username; ?>" />
            <input type="password" id="password" name="password" value="" />
            <input type="submit" id="submit" value="login" />
        </form>
    <?php } else { ?>
    <a id="click_to_login" href="?action=logout">click here to logout</a>
    <?php } ?>

    <h1>Skor Project Status</h1>
    <h3>Last updated: <?php echo $project_status->last_updated() ;?></h3>

    <div id="gradient">
        <ul class="projects">

            <?php foreach($project_status->get_all_projects() as $group) { ?>
            <li>
                <h2><?php echo $group->group_title;?></h2>
            <li>
            
            <?php foreach($group->projects as $project) { ?>
            <li class="individual_project" id="project_id_<?php echo $project->id; ?>">
                <p class="title"><?php echo $project->title;?></p>
                <?php if(!is_null($project->link)) { ?>
                    <a class="extlink" href="<?php echo $project->link->url;?>"><?php echo $project->link->text; ?></a>
                <?php } ?>
                <div class="status">
                    <span class="percent p<?php echo $project->complete; ?>"><?php echo $project->complete; ?>%</span>
                </div>
                <p class="notes">
                    <?php echo $project->notes; ?>
                </p>
            </li>
            <?php } } ?>
        </ul>
    </div>

    <div id="project_template">
        <li>
            <p class="title"></p>
            <a class="extlink" href=""></a>
            <div class="status"><span></span></div>
            <p class="notes"></p>
        </li>
    </div>

    <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'></script>

    <script type="text/javascript">
        $(document).ready(function() 
        {
            $('#gradient').fadeIn(1900);
            $("#click_to_login").click(function () {
                $("#login").slideToggle();
            })

            $(".message").click(function () {
                if($(this).attr('id') != 'inner_message') {
                    $(this).slideUp('slow');
                }
            });

            /****** Updating Group Title   ******/
            $(document).on('click', '.title', (function () {
                var classes = $(this).attr('class');
                if($(this).is('p')) {
                    $(this).replaceWith($('<textarea class="' + classes + '">' + this.innerHTML + '</textarea>'));
                } 
            }));

            /****** Updating Project Title ******/
            $(document).on('click', '.title', (function () {
                var classes = $(this).attr('class');
                if($(this).is('p')) {
                    $(this).replaceWith($('<textarea class="' + classes + '">' + this.innerHTML + '</textarea>'));
                } 
            }));

            $(document).on('change', '.title', (function () {
                var id = $(this).parent().attr('id').replace('project_id_','');
                var ids = id.split('_');
                var gid = ids[0];
                var pid = ids[1];

                var post_data = 'gid='+gid+'&pid='+pid+'&title='+$(this).val();
                //var post_data = 'gid='+gid+'&pid='+pid;

                $.ajax({
                    type: 'POST',
                    url: '?action=update_title',
                    data: post_data,
                    statusCode: {
                        404: function() {
                          alert("page not found");
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(errorThrown);
                      //  console.log('[textStatus: '+textStatus+']', '[errorThrown: '+errorThrown+']');
                    }/*,
                    success: function(data, textStatus, jqXHR) {
                        alert(data);
                    }*/
                });
            }));

            $(document).on('blur', '.title', (function () {
                var classes = $(this).attr('class');
                if($(this).is('textarea')) {
                    $(this).replaceWith($('<p class="' + classes +'">' + $(this).val() + '</p>'));
                }
            }));
        });
    </script>

    <div id="footer">
        &copy <?php echo date('Y'); ?> Voltamp Media, Inc.
        <a id="voltampmedia" href="http://voltampmedia.com">http://voltampmedia.com</a> 
        <a id="twitter" href="http://twitter.com/voltampmedia">@voltampmedia</a>

    </div>
</body>
</html>