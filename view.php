<?php if(!defined('project_status')) die('uh, nope!') ?>
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

<h1>Skor Project Status</h1>
<h3>Last updated: <?php echo $project_status->last_updated() ;?></h3>

    <div id="gradient">
        <ul class="projects">

            <?php foreach($project_status->get_all_projects() as $group) { ?>

            <li>
                <h2><?php echo $group->group_title;?></h2>
            <li>
            <li>
                <?php foreach($group->projects as $project) { ?>
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
            $( '#gradient' ).fadeIn(1900);      
        });
    </script>

    <div id="footer">
        &copy <?php echo date('Y'); ?> Voltamp Media, Inc.
        <a id="voltampmedia" href="http://voltampmedia.com">http://voltampmedia.com</a> 
        <a id="twitter" href="http://twitter.com/voltampmedia">@voltampmedia</a>

    </div>
</body>
</html>