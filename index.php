<?php
/*
Plugin Name: LingOn
Plugin URI: http://www.ling-on.com
Description: LingOn Functions
Version: 0.5
Author: Jang Dukmin
Author URI: http://www.ling-on.com
License: GPLv2 or later
*/

//Debug File Location : /var/www/html/wp-content/debug.log
//Debug_mod = false로 두면 Debug.log에 입력됩니다.
//디버그로그 쌓이면 파일용량이 너무 올라가서 평소엔 true로 놔두시다가 디버깅하실때 false로 놓고 사용하시면됩니다.
include_once 'class/login.php';
include_once 'class/moim.create.php';
include_once 'class/moim.builder.php';

//클래스 생성 후 항상 변수에 선언
//add_shortcode : 숏코드의 형태로 함수를 실행할 수 있도록 숏코드 리스트에 등록

$LingOn_Login = new JDM_Login();
add_shortcode( 'JDM_LingOn_LogStateHeader' , array( $LingOn_Login, 'LoginHeader_Displayer_JDM' ) );

$Moim_Info = new JDM_Moim_Create();
$Resister = new JDM_moim_resister();
add_shortcode('JDM_redirection' , array( $Resister, 'resister_insert' ) );
add_shortcode('OSH_check_register', array($Resister,'resister_moim'));

add_filter( 'the_content', 'Moim_Builder', 1);
add_filter( 'ninja_forms_submission_actions', 'my_ninja_forms_submission_actions', 10, 2 );

add_action( 'ninja_forms_after_submission', 'update_post');


