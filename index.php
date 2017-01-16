<?php
include('app.php');
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FBWtube</title>

    <link debug="false" href="./assets/css/application.css" media="all" rel="stylesheet" />
    <link debug="false" href="./assets/css/bootstrap.css" media="all" rel="stylesheet" />
    <script src="./assets/js/application.js"></script>

    <link rel="stylesheet" href="./bower_components/material-design-lite/material.min.css">

    <link href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <!-- Color -->
    <link rel="stylesheet" href="https://storage.googleapis.com/code.getmdl.io/1.0.2/material.grey-red.min.css" />

    <!-- <script src="//code.jquery.com/jquery-1.11.3.min.js"></script> -->

    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/mooc.css">
  </head>
  <body>

  <div class="demo-layout mdl-layout mdl-js-layout mdl-layout--fixed-header">
    <main class="mdl-layout__content mdl-color--grey-200">
      <div class="mdl-grid demo-content">

        <?php
        if(!isset($_GET['chapter'])){
          #include ('chapters.php');
          $_GET['chapter'] = 0;
        }
        $chapterId = $_GET['chapter'];
        $teacher = getVimeoIdFromUrl($chapters[$chapterId]->videos->url_teacher);
        $presentation = getVimeoIdFromUrl($chapters[$chapterId]->videos->url_presentation);

        $chapterTitle = $chapters[$chapterId]->title;
        //$chapterdescription = $chapters[$chapterId]->description;
        ?>

        <div class="mdl-cell mdl-cell--12-col mdl-cell--12-col-phone mdl-cell--12-col-tablet mdl-grid">
          <div class="mooc-chapter-nav mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp">
            <?php
            foreach ($chapters as $key => $chapter) {
              $url = '?id='.$lectureId.'&chapter='.$key.'';

              if($key == $chapterId){
                $cssnav = 'mdl-button--raised mdl-button--accent';
              }
              else {
                $cssnav = '';
              }
              echo '
              <a id="tt'.$key.'" href="'.$url.'" class="mdl-button mdl-js-button '.$cssnav.'">
                <i class="material-icons">ondemand_video</i>
              </a>
              <div class="mdl-tooltip" for="tt'.$key.'">
                '.$chapter->title.'
              </div>
              ';
            }
            ?>
          </div>

          <div class="mooc-videoplayer mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">

            <div class="mdl-card__title" >
              <h2 class="mdl-card__title-text">
                <?php echo $chapterTitle ?>
        			</h2>
            </div>

            <div class="mdl-card__supporting-text">

              <?php

              $teacherObj = callVimeoApiByVideoId($teacher);

              $presentationObj = callVimeoApiByVideoId($presentation);

              if(isset($teacherObj->message)){
                echo '<h2>'.$teacherObj->title.'</h2>';
                echo $teacherObj->message;
              }
              elseif(isset($presentationObj->message)){

                echo '<h2>'.$presentationObj->title.'</h2>';
                echo $presentationObj->message;
              }
              else{

              ?>

              <div class="televideoplayer" data-lanalytics-resource="uid" data-ratio="20" data-speed="1" data-subtitles="" data-transcript="" data-videodata='{
                "hasLoadingOverlay":false,
                "hasInteractiveTranscript":true,
                "streams":
                  {
                    "left": <?php echo getValuesFromVimeo($teacherObj) ?>,
                    "right": <?php echo getValuesFromVimeo($presentationObj) ?>

                  }
                }'
              data-volume="0.9244186046511628"></div>
              <script type="text/javascript">
                new Html5Player.VideoPlayer($('.televideoplayer'), $('.televideoplayer').data('videodata'));
              </script>
              <?php } ?>
            </div>
          </div>

          <div class="mooc-description mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">
            <div class="mdl-card__title" style="display:block;">
              <h2 class="mdl-card__title-text">
                <?php echo $course->title; ?> - <?php echo $course->lectureTitle; ?>
        			</h2>
            </div>
            <div class="mdl-card__supporting-text">
              <div class="mooc-lecture-description">

              </div>

              <div class="mooc-lecture-lecturer">
                <div class="fhb-logo">
                  <img src="./assets/img/logo_fbw.jpg">
                </div>

                <div class="mooc-lecturer--contact">
                  <strong><?php echo $course->lecturer; ?> </strong><br />

                  <a href="mailto:<?php echo $course->lecturerMail; ?>"><?php echo $course->lecturerMail; ?></a>
                </div>




              </div>
            </div>
          </div>
        </div>
    </main>

    <script src="./bower_components/material-design-lite/material.min.js"></script>

    <script src="./assets/js/jquery.truncate.min.js"></script>
    <!-- <script src="./assets/js/custom.js"></script> -->

  </body>
</html>
