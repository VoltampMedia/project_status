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
        <title><?php echo $project_status->h1_title() ;?></title>

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

    <h1 id="h1title"><?php echo $project_status->h1_title(); ?></h1>
    <h3>Last updated: <?php echo $project_status->last_updated() ;?></h3>

    <div id="gradient">
        <ul class="projects">

            <?php if($project_status->loggedin()) { ?>
            <span class="addGroup">add another group</span>
            <form id="addGroup" method="post" action="?action=add_group">
                <label for="group">Group</label><input type="text" name="group" value="<?php echo $group; ?>" />
            <input type="submit" id="addGroupSubmit" value="Add Group" />
            </form>
            <br />
            <span class="addProject">add another project</span>
            <form id="addProject" method="post" action="?action=add_project">
                <label for="title">Title</label><input type="text" name="title" value="<?php echo $title; ?>" />
                <label for="notes">Notes</label><input type="text" name="notes" class="addProjectNotes" value="<?php echo $notes; ?>" />
                <label for="percent">Completed</label>
                <select class="addProjectPercent" name="percent">
                    <option value="0">0%</option>
                    <option value="10">10%</option>
                    <option value="20">20%</option>
                    <option value="30">30%</option>
                    <option value="40">40%</option>
                    <option value="50">50%</option>
                    <option value="60">60%</option>
                    <option value="70">70%</option>
                    <option value="80">80%</option>
                    <option value="90">90%</option>
                    <option value="100">100%</option>
                </select>
                <select name="group">
                    <?php foreach($project_status->get_all_projects() as $gid => $group) { ?>
                        <option value="<?php echo $gid;?>"><?php echo $group['group_title']; ?></option>
                    <?php } ?>
                </select>
            <input type="submit" id="addProjectSubmit" value="Add Project" />
            </form>

            <?php } ?>
            <div id="formspacer"></div>

            <?php foreach($project_status->get_all_projects() as $gid => $group) { ?>
            
            <h2 class="grouptitle" id="group_id_<?php echo $gid; ?>"><?php echo $group['group_title'];?></h2>
            
            <?php if($project_status->loggedin()) { ?>
            <a class="removegroup" href="?action=remove_group&gid=<?php echo $gid; ?>">remove group</a>
            <?php } ?>

            <?php foreach($group['projects'] as $project) { ?>
            <li class="individual_project" id="project_id_<?php echo $project['id']; ?>">
                <p class="title"><?php echo $project['title'];?></p>

                <?php if($project_status->loggedin()) { ?>
                <a class="removeproject" href="?action=remove_project&gpid=<?php echo $project['id']; ?>">remove project</a>
                <?php } ?>
                <?php /*if(!is_null($project->link)) { ?>
                    <a class="extlink" href="<?php echo $project->link->url;?>"><?php echo $project->link->text; ?></a>
                <?php } */?>
                <div class="status">
                    <span class="percent p<?php echo $project['complete']; ?>"><?php echo $project['complete']; ?>%</span>
                </div>
                <p class="notes"><?php echo $project['notes']; ?></p>
            </li>
            <?php } } ?>
        </ul>
    </div>

    <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'></script>

    <script type="text/javascript">
        $(document).ready(function() 
        {
            $('#gradient').fadeIn(1900);
            $("#click_to_login").click(function () {
                $("#login").slideToggle();
            })

            $(".addProject").click(function () {
                $("#addProject").slideToggle();
            })

            $(".addGroup").click(function () {
                $("#addGroup").slideToggle();
            })

            $(document).on('click', '.message', (function () {
                $(this).slideUp('slow');
            }));

            $('.success-msg').delay(10000).slideUp('slow');
            $('.info-msg').delay(10000).slideUp('slow');

            <?php if($project_status->loggedin()) { ?>
            /****** Updating Project Completion ******/

            $(document).on('click', '.percent', (function () {
                var classes = $(this).attr('class');
                if($(this).is('span')) {
                    var textarea = $('<textarea class="' + classes + '">' + this.innerHTML + '</textarea>');
                    $(this).replaceWith(textarea);
                    textarea.trigger('focus');
                } 
            }));

            $(document).on('change', '.percent', (function () {
                var id = $(this).parent().parent().attr('id').replace('project_id_','');
                var ids = id.split('_');
                var gid = ids[0];
                var pid = ids[1];

                var post_data = 'gid='+gid+'&pid='+pid+'&percent='+$(this).val();

                $.ajax({
                    type: 'POST',
                    url: '?action=update_percent',
                    data: post_data,
                    dataType: "json",
                    success: function (data, textStatus, jqXHR) {
                        if(data.response) {
                            $("#msg_container").append("<div class=\"message success-msg\"><h5>Success:</h5>" + data.data + "</div>").delay(10000).slideUp('slow');
                        } else {
                            $("#msg_container").append("<div class=\"message error-msg\"><h5>Error:</h5>" + data.data + "</div>");
                        }
                    }
                });
            }));

            $(document).on('blur', '.percent', (function () {
                $(this).removeClass('p0 p10 p20 p30 p40 p50 p60 p70 p80 p90 p100');
                var classes = $(this).attr('class');
                if($(this).is('textarea')) {
                    var percent_raw = $(this).val().replace('%','');
                    var percent = Math.round(percent_raw/10)*10;
                    classes = classes + ' p' + percent;
                    $(this).replaceWith($('<span class="' + classes +'">' + percent + '%</span>'));
                }
            }));

            /****** 
                        Updating  H! Title   
            ******/
            $(document).on('click', '#h1title', (function () {
                var id = $(this).attr('id');
                if($(this).is('h1')) {
                    var textarea = $('<textarea id="' + id + '">' + this.innerHTML + '</textarea>');
                    $(this).replaceWith(textarea);
                    textarea.trigger('focus');
                } 
            }));

            $(document).on('change', '#h1title', (function () {
               
                var post_data = 'h1title='+$(this).val();

                $.ajax({
                    type: 'POST',
                    url: '?action=update_h1title',
                    data: post_data,
                    dataType: "json",
                    success: function (data, textStatus, jqXHR) {
                        console.log(data);
                        if(data.response) {
                            $("#msg_container").append("<div class=\"message success-msg\"><h5>Success:</h5>" + data.data + "</div>").delay(10000).slideUp('slow');;
                        } else {
                            $("#msg_container").append("<div class=\"message error-msg\"><h5>Error:</h5>" + data.data + "</div>");
                        }
                    }
                });
            }));

            $(document).on('blur', '#h1title', (function () {
                var id = $(this).attr('id');
                if($(this).is('textarea')) {
                    $(this).replaceWith($('<h1 id="' + id + '">' + $(this).val() + '</h1>'));
                }
            }));

            /****** 
                        Updating Group Title   
            ******/
            $(document).on('click', '.grouptitle', (function () {
                var classes = $(this).attr('class');
                var id = $(this).attr('id');
                if($(this).is('h2')) {
                    var textarea = $('<textarea id="' + id + '" class="' + classes + '">' + this.innerHTML + '</textarea>');
                    $(this).replaceWith(textarea);
                    textarea.trigger('focus');
                } 
            }));

            $(document).on('change', '.grouptitle', (function () {
                var gid = $(this).attr('id').replace('group_id_','');
               
                var post_data = 'gid='+gid+'&title='+$(this).val();

                $.ajax({
                    type: 'POST',
                    url: '?action=update_group_title',
                    data: post_data,
                    dataType: "json",
                    success: function (data, textStatus, jqXHR) {
                        console.log(data);
                        if(data.response) {
                            $("#msg_container").append("<div class=\"message success-msg\"><h5>Success:</h5>" + data.data + "</div>").delay(10000).slideUp('slow');;
                        } else {
                            $("#msg_container").append("<div class=\"message error-msg\"><h5>Error:</h5>" + data.data + "</div>");
                        }
                    }
                });
            }));

            $(document).on('blur', '.grouptitle', (function () {
                var classes = $(this).attr('class');
                var id = $(this).attr('id');
                if($(this).is('textarea')) {
                    $(this).replaceWith($('<h2 id="' + id + '" class="' + classes +'">' + $(this).val() + '</h2>'));
                }
            }));


            /****** Updating Project Notes ******/
            $(document).on('click', '.notes', (function () {
                var classes = $(this).attr('class');
                if($(this).is('p')) {
                    var textarea = $('<textarea class="' + classes + '">' + this.innerHTML + '</textarea>');
                    $(this).replaceWith(textarea);
                    textarea.trigger('focus');
                } 
            }));

            $(document).on('change', '.notes', (function () {
                var id = $(this).parent().attr('id').replace('project_id_','');
                var ids = id.split('_');
                var gid = ids[0];
                var pid = ids[1];

                var post_data = 'gid='+gid+'&pid='+pid+'&notes='+$(this).val();

                $.ajax({
                    type: 'POST',
                    url: '?action=update_notes',
                    data: post_data,
                    dataType: "json",
                    success: function (data, textStatus, jqXHR) {
                        console.log(data);
                        if(data.response) {
                            $("#msg_container").append("<div class=\"message success-msg\"><h5>Success:</h5>" + data.data + "</div>").delay(10000).slideUp('slow');;
                        } else {
                            $("#msg_container").append("<div class=\"message error-msg\"><h5>Error:</h5>" + data.data + "</div>");
                        }
                    }
                });
            }));

            $(document).on('blur', '.notes', (function () {
                var classes = $(this).attr('class');
                if($(this).is('textarea')) {
                    $(this).replaceWith($('<p class="' + classes +'">' + $(this).val() + '</p>'));
                }
            }));

            /****** Updating Project Title ******/
            $(document).on('click', '.title', (function () {
                var classes = $(this).attr('class');
                if($(this).is('p')) {
                    var textarea = $('<textarea class="' + classes + '">' + this.innerHTML + '</textarea>');
                    $(this).replaceWith(textarea);
                    textarea.trigger('focus');
                } 
            }));

            $(document).on('change', '.title', (function () {
                var id = $(this).parent().attr('id').replace('project_id_','');
                var ids = id.split('_');
                var gid = ids[0];
                var pid = ids[1];

                var post_data = 'gid='+gid+'&pid='+pid+'&title='+$(this).val();

                $.ajax({
                    type: 'POST',
                    url: '?action=update_title',
                    data: post_data,
                    dataType: "json",
                    success: function (data, textStatus, jqXHR) {
                        console.log(data);
                        if(data.response) {
                            $("#msg_container").append("<div class=\"message success-msg\"><h5>Success:</h5>" + data.data + "</div>").delay(10000).slideUp('slow');
                        } else {
                            $("#msg_container").append("<div class=\"message error-msg\"><h5>Error:</h5>" + data.data + "</div>");
                        }
                    }
                });
            }));

            $(document).on('blur', '.title', (function () {
                var classes = $(this).attr('class');
                if($(this).is('textarea')) {
                    $(this).replaceWith($('<p class="' + classes +'">' + $(this).val() + '</p>'));
                }
            }));

            <?php } ?>

        });
    </script>

    <div id="footer">
        &copy <?php echo date('Y'); ?> Voltamp Media, Inc.
        <a id="voltampmedia" href="http://voltampmedia.com">http://voltampmedia.com</a> 
        <a id="twitter" href="http://twitter.com/voltampmedia">@voltampmedia</a>

    </div>
</body>
</html>