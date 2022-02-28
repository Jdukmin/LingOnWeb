<?php

class JDM_Moim_Create
{
	

	// private $form_fields, $new_moim_product, $new_product_id, $title, $description;
	// private $new_moim_page, $new_moim_page_id;
	private $dataset = array();
	private $board;

	// dataset structure
	// array
	// 'creater' => $dataset['creater'],
	// 'title'=>$dataset['title'],
	// 'description'=>$dataset['description'],
	// 'product_id'=>$dataset['moim_product_id'],
	// 'kboard_id'=>$dataset['moim_kboard_id'],
	// 'review_id'=>wp_insert_post( $postarr ),
	// 'category'=>$dataset['category'],
	// 'pnum'=>$dataset['pnum'],
	// 'start_date'=>$dataset['start_date'],
	// 'which_day'=>$dataset['which_day'],
	// 'end_date'=>$dataset['end_date'],
	// 'time'=>$dataset['time'],
	// 'start_time'=>$dataset['start_time']

	//__construct()함수 : 사이트를 로드할 때마다 실행하는 함수들로 보통 조건문(액션문)을 추가
	//액션(워드프레스 고유 기능)
	////워드프레스에서 특정 함수(함수 1) 중간에 액션을 추가할 수 있음
	////다른 함수(함수 2)에서 그 액션을 사용(add_action)하면 함수(함수 1) 중간의 액션의 위치에서 다른 함수(함수 2)를 실행할 수 있음
	////따라서 이 선언을 __construct()에 해줘야 사이트에서 이 기능을 호출 가능함
	////액션 리스트는 플러그인 공식 페이지에 있으며 액션을 어떻게 사용해야 되는지도 나와 있음.(안나와있는것도 있음)
	//필터(워드프레스 고유 기능)
	////액션과 같은 매커니즘으로 특정 함수(함수 1) 중간에 필터를 추가할 수 있음
	////다른 함수(함수 2)에서 그 필터를 사용하면 함수(함수 1) 중간의 필터의 위치에서 그 함수를 "수정"하여 다른 함수(함수 2)를 실행할 수 있음.
	////현재 이 코드에선 쓰이지 않음, css(웹디자인 언어) 변경(디자인 변경)에 주로 쓰임.

	//현재 이 함수에서는 입력폼 플러그인인 ninjaform의 액션 ninja_forms_after_submission(폼 제출 시 실행)에 함수를 연결하는 "후크"를 진행 함.
	public function __construct()
	{
		add_action( 'ninja_forms_after_submission', array($this, 'create_moim') );
	}

	//액션이 "후크"되는 함수, 후크 될 때 변수도 같이 넘겨받을 수 있음(단, 이것은 액션 선언 시에 넘겨받을 변수를 받으므로 다른 플러그인에서 쓰는 변수들을 그대로 따와야함.)
	//$form_data같은 경우 ninjaform 내부에서 사용되는 사용자 입력 정보(array형태)를 변수로 넘겨받아온 상태임.
	//$form_data[ 'form_id' ] : 폼 아이디
	//$form_data[ 'fields' ] : 폼 입력 데이터
	//$form_data[ 'fields' ][ '5' ] : 모임 생성 폼(form id = 2)의 모임 이름 입력 칸을 가리킴. 이 5번 id는 database상에 저장되어 있음.
	//$form_data[ 'fields' ][ '5' ][ 'value' ] : 5번 폼(모임 이름 입력 칸)에 사용자가 입력한 값을 가리킴.
	public function create_moim( $form_data )
	{

		if ( $form_data[ 'form_id' ] == 2)
		{
			global $wpdb;
			//입력 받은 폼이 모임 생성 폼이면 다음 프로세스를 실행
			//1. 미리보기 페이지 생성(우커머스 페이지, post_type : 'product')
			//2. 모임 진행 페이지 생성(페이지, post_type : 'example') -> 템플릿(페이지 구성 중간 단위)에는 post_type을 설정할 수 있어 추후 동적페이지 생성 가능
			//모임 진행 페이지에 들어가는 정보 : kboard id, review id(이것들은 템플릿 내에 라이브러리 숏코드 형태로 들어감)
			//이 부분이 9월에 완성할 부분이고 이해가 안되면 바로 연락주세요!
			$this->dataset['moim_product_id'] = $this->create_product_from_ninjaform( $form_data );
			$this->dataset['moim_kboard_id'] = $this->create_kboard_from_ninjaform( $form_data );
			// ob_start();
			// var_dump($this->dataset);
			// error_log(ob_get_clean());
			
			//OSH_add_submitted_data_on_db();

		$postarr = array(
			'post_title' => $this->dataset['title'],
			'post_content' => '',//1485 : 소개 메인 kboard: 각각의 id에 맞는 kboard 생성
			'post_status' => 'publish',//kboard, 참여 버튼만 숏코드로 기능을 남기고 나머지는 css 작업을 하는게 깔끔할 것 같음.
			'post_type' => 'moim_page'
		);

		$this->dataset['post_id'] = wp_insert_post( $postarr );

					//db에 data 전송
					$wpdb->insert( "wp_moim", array(
						'creater' => $this->dataset['creater'],
						'title'=>$this->dataset['title'],
						'description'=>$this->dataset['description'],
						'product_id'=>$this->dataset['moim_product_id'],
						'kboard_id'=>$this->dataset['moim_kboard_id'],
						'post_id'=>$this->dataset['post_id'],
						'category'=>$this->dataset['category'],
						'pnum'=>$this->dataset['pnum'],
						'start_date'=>$this->dataset['start_date'],
						'which_day'=>$this->dataset['which_day'],
						'end_date'=>$this->dataset['end_date'],
						'time'=>$this->dataset['time'],
						'start_time'=>$this->dataset['start_time'],
						'link'=>$this->dataset['link']
					),
				array('%d','%s','%s','%d','%d','%d','%s','%s','%s','%s','%s','%s','%s'));



			// ob_start();
			// var_dump($post_id);
			// error_log(ob_get_clean());

		//wp_redirect("https://www.ling-on.com/?moim_page=".$this->dataset['title']);
		//exit();

		//redirect 해결해야함
		}else{
			// wp_redirect("https://www.ling-on.com/");
			//exit();
		}


	}

	//상품 페이지(모임 미리보기 페이지)생성 함수
	//
	public function create_product_from_ninjaform( $form_data ){
		if ( $form_data[ 'form_id' ] == 2)
		{
			$form_fields = $form_data[ 'fields' ];
			
			$title = $form_fields[ 5 ][ 'value' ];
			$description = $form_fields[ 6 ][ 'value' ];
			
			//wp_insert_post() : 페이지를 생성해주는 워드프레스 내장 함수
			//이 함수에 들어가는 인자는 페이지에 대한 정보여야 함. 이 정보들을 사용자에게서 입력받아 함수를 실행시키는 방식
			$new_moim_product = array(
				'post_title' => $title,
				'post_content' => $description,
				'post_status' => 'publish',
				'post_type' => 'product',
			);
			$new_product_id = wp_insert_post( $new_moim_product );

			//error_log : debug.log 파일에 우리가 원하는 에러 로그도 추가시킬 수 있는데,
			//다음과 같은 식으로 추가를 하게 되면 $form_fields[ 13 ][ 'value' ]에 해당하는 값이 debug.log에 추가가 됨.
			//변수 값을 확인할 수 있는 수단
			error_log($form_fields[ 13 ][ 'value' ]);

			//카테고리 ID(우커머스 상품 페이지 id를 가리킴. 데이터베이스에 id가 등록되어있는걸로 추정, 사이트 url에도 들어가있음.)
			//스터디 : 30
			//줌독서실 : 29
			//1 : 스터디, 2 : 줌독서실, 0 : 없음
			if ( $form_fields[ 13 ][ 'value' ] == 1 )
			{
				wp_set_object_terms($new_product_id, 30, 'product_cat', true);
			}
			elseif ( $form_fields[ 13 ][ 'value' ] == 2 )
			{
				wp_set_object_terms($new_product_id, 29, 'product_cat', true);
			}

			// 모임 미리보기 페이지를 생성시켜 준 후 사용자를 이동시키는(redirection) 워드프레스 내장 함수
			// 현재 디버깅이 안되어 비활성화
			// wp_redirect( site_url()."?post=".$post_id);
			
			//미리보기 페이지의 page id 반환, db에 등록 해야함.
			return $new_product_id;
		}
		else
		{
			return -1;
		}
	}

	//게시판 생성 함수
	//위의 함수와 비교해서 보시면 편할 듯 합니다.
	public function create_kboard_from_ninjaform( $form_data ){
		if ( $form_data[ 'form_id' ] == 2)
		{	
			global $wpdb;
			$form_fields = $form_data[ 'fields' ];
			$dataset = array(
				'creater' => get_current_user_id(),
				'title' => $form_fields[5]['value'],
				'description' =>$form_fields[6]['value'],
				'category' => $form_fields[13]['value'],
				'pnum'=>$form_fields[14]['value'],
				'start_date' => $form_fields[17]['value'],
				'which_day' => $form_fields[25]['value'],
				'end_date' => $form_fields[21]['value'],
				'time'=>$form_fields[22]['value'],
				'start_time'=>$form_fields[24]['value']['hour'].':'.$form_fields[24]['value']['minute'].':'.'00',
				'link'=>$form_fields[26]['value'],
			);
			foreach($dataset as $key=>$value){
				$this->dataset[$key]=$value;
			}



			if(!defined('KBOARD_COMMNETS_VERSION')) die('<script>alert("게시판 생성 실패!\nKBoard 댓글 플러그인을 설치해주세요.\nhttps://www.cosmosfarm.com/ 에서 다운로드 가능합니다.");history.go(-1);</script>');
			if(!current_user_can('manage_options')) wp_die(__('You do not have permission.', 'kboard'));
				
			header('Content-Type: text/html; charset=UTF-8');
			
			// $_POST = stripslashes_deep($_POST);
			
			// $board_id         = isset($_POST['board_id'])         ? intval($_POST['board_id'])                               : '';
			// $board_name       = isset($_POST['board_name'])       ? esc_sql(sanitize_text_field($_POST['board_name']))       : '';
			// $skin             = isset($_POST['skin'])             ? esc_sql(sanitize_text_field($_POST['skin']))             : '';
			// $page_rpp         = isset($_POST['page_rpp'])         ? esc_sql(sanitize_text_field($_POST['page_rpp']))         : '';
			// $use_comment      = isset($_POST['use_comment'])      ? esc_sql(sanitize_text_field($_POST['use_comment']))      : '';
			// $use_editor       = isset($_POST['use_editor'])       ? esc_sql(sanitize_text_field($_POST['use_editor']))       : '';
			// $permission_read  = isset($_POST['permission_read'])  ? esc_sql(sanitize_text_field($_POST['permission_read']))  : '';
			// $permission_write = isset($_POST['permission_write']) ? esc_sql(sanitize_text_field($_POST['permission_write'])) : '';
			// $admin_user       = isset($_POST['admin_user'])       ? implode(',', array_map('esc_sql', array_map('sanitize_text_field', explode(',', $_POST['admin_user']))))     : '';
			// $use_category     = isset($_POST['use_category'])     ? esc_sql(sanitize_text_field($_POST['use_category']))     : '';
			// $category1_list   = isset($_POST['category1_list'])   ? implode(',', array_map('esc_sql', array_map('sanitize_text_field', explode(',', $_POST['category1_list'])))) : '';
			// $category2_list   = isset($_POST['category2_list'])   ? implode(',', array_map('esc_sql', array_map('sanitize_text_field', explode(',', $_POST['category2_list'])))) : '';
			
			$nowid = 0;
			$board_id         = $nowid;
			$board_name       = $this->dataset['title'];
			$skin             = 'default';
			$page_rpp         = intval(10);
			$use_comment      = 'yes';
			$use_editor       = '';
			$permission_read  = 'all';
			$permission_write = 'all';
			$admin_user       = '';
			$use_category     = '';
			$category1_list   = '';
			$category2_list   = '';


			$auto_page = isset($_POST['auto_page']) ? intval($_POST['auto_page']) : '';
			// if($auto_page){
			// 	$auto_page_board_id = $wpdb->get_var("SELECT `board_id` FROM `{$wpdb->prefix}kboard_board_meta` WHERE `key`='auto_page' AND `value`='$auto_page'");
			// 	if($auto_page_board_id && $auto_page_board_id != $board_id){
			// 		$auto_page = '';
			// 		echo '<script>alert("게시판 자동 설치 페이지에 이미 연결된 게시판이 존재합니다. 페이지당 하나의 게시판만 설치 가능합니다.");window.history.go(-1);</script>';
			// 		exit;
			// 	}
			// }
			
			if(!$board_id){
				$wpdb->insert(
					"{$wpdb->prefix}kboard_board_setting",
					array(
						'board_name'       => $board_name,
						'skin'             => $skin,
						'page_rpp'         => $page_rpp,
						'use_comment'      => $use_comment,
						'use_editor'       => $use_editor,
						'permission_read'  => $permission_read,
						'permission_write' => $permission_write,
						'admin_user'       => $admin_user,
						'use_category'     => $use_category,
						'category1_list'   => $category1_list,
						'category2_list'   => $category2_list,
						'created'          => date('YmdHis', current_time('timestamp'))
					),
					array('%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
				);
				$board_id = $wpdb->insert_id;
				$wpdb->insert(
					"{$wpdb->prefix}kboard_board_meta",
					array(
						'board_id'       => $board_id,
						'key'			=> 'comment_skin',
						'value'			=> 'default'
					),
					array('%d', '%s', '%s')
				);
				$wpdb->insert(
					"{$wpdb->prefix}kboard_board_meta",
					array(
						'board_id'       => $board_id,
						'key'			=> 'comment_plugin_now',
						'value'			=> '10'
					),
					array('%d', '%s', '%s')
				);
				$wpdb->insert(
					"{$wpdb->prefix}kboard_board_meta",
					array(
						'board_id'       => $board_id,
						'key'			=> 'list_sort_numbers',
						'value'			=> 'desc'
					),
					array('%d', '%s', '%s')
				);
				$wpdb->insert(
					"{$wpdb->prefix}kboard_board_meta",
					array(
						'board_id'       => $board_id,
						'key'			=> 'list_total',
						'value'			=> '0'
					),
					array('%d', '%s', '%s')
				);
				$wpdb->insert(
					"{$wpdb->prefix}kboard_board_meta",
					array(
						'board_id'       => $board_id,
						'key'			=> 'pass_autop',
						'value'			=> 'disable'
					),
					array('%d', '%s', '%s')
				);
				$wpdb->insert(
					"{$wpdb->prefix}kboard_board_meta",
					array(
						'board_id'       => $board_id,
						'key'			=> 'permission_admin_roles',
						'value'			=> 'a:1:{i:0;s:13:"administrator";}'
					),
					array('%d', '%s', '%s')
				);
				$wpdb->insert(
					"{$wpdb->prefix}kboard_board_meta",
					array(
						'board_id'       => $board_id,
						'key'			=> 'permission_attatchment_download_roles',
						'value'			=> 'a:1:{i:0;s:13:"administrator";}'
					),
					array('%d', '%s', '%s')
				);
				$wpdb->insert(
					"{$wpdb->prefix}kboard_board_meta",
					array(
						'board_id'       => $board_id,
						'key'			=> 'permission_comment_write_roles',
						'value'			=> 'a:1:{i:0;s:13:"administrator";}'
					),
					array('%d', '%s', '%s')
				);
				$wpdb->insert(
					"{$wpdb->prefix}kboard_board_meta",
					array(
						'board_id'       => $board_id,
						'key'			=> 'permission_order_roles',
						'value'			=> 'a:1:{i:0;s:13:"administrator";}'
					),
					array('%d', '%s', '%s')
				);
				$wpdb->insert(
					"{$wpdb->prefix}kboard_board_meta",
					array(
						'board_id'       => $board_id,
						'key'			=> 'permission_read_roles',
						'value'			=> 'a:1:{i:0;s:13:"administrator";}'
					),
					array('%d', '%s', '%s')
				);
				$wpdb->insert(
					"{$wpdb->prefix}kboard_board_meta",
					array(
						'board_id'       => $board_id,
						'key'			=> 'permission_reply_roles',
						'value'			=> 'a:1:{i:0;s:13:"administrator";}'
					),
					array('%d', '%s', '%s')
				);
				$wpdb->insert(
					"{$wpdb->prefix}kboard_board_meta",
					array(
						'board_id'       => $board_id,
						'key'			=> 'permission_vote_roles',
						'value'			=> 'a:1:{i:0;s:13:"administrator";}'
					),
					array('%d', '%s', '%s')
				);
				$wpdb->insert(
					"{$wpdb->prefix}kboard_board_meta",
					array(
						'board_id'       => $board_id,
						'key'			=> 'permission_write_roles',
						'value'			=> 'a:1:{i:0;s:13:"administrator";}'
					),
					array('%d', '%s', '%s')
				);
				$wpdb->insert(
					"{$wpdb->prefix}kboard_board_meta",
					array(
						'board_id'       => $board_id,
						'key'			=> 'skin_fields',
						'value'			=> 'a:12:{s:5:"title";a:10:{s:5:"class";s:17:"kboard-attr-title";s:12:"close_button";s:0:"";s:10:"field_type";s:5:"title";s:11:"field_label";s:6:"제목";s:10:"permission";s:3:"all";s:10:"field_name";s:0:"";s:8:"meta_key";s:5:"title";s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:11:"description";s:0:"";}s:6:"option";a:11:{s:5:"class";s:18:"kboard-attr-option";s:12:"close_button";s:3:"yes";s:10:"field_type";s:6:"option";s:11:"field_label";s:6:"옵션";s:10:"field_name";s:0:"";s:8:"meta_key";s:6:"option";s:17:"secret_permission";s:3:"all";s:6:"secret";a:1:{i:0;s:13:"administrator";}s:17:"notice_permission";s:5:"roles";s:6:"notice";a:1:{i:0;s:13:"administrator";}s:11:"description";s:0:"";}s:6:"author";a:10:{s:5:"class";s:18:"kboard-attr-author";s:12:"close_button";s:0:"";s:10:"field_type";s:6:"author";s:11:"field_label";s:9:"작성자";s:10:"field_name";s:0:"";s:8:"meta_key";s:6:"author";s:10:"permission";s:0:"";s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:11:"description";s:0:"";}s:9:"category1";a:10:{s:5:"class";s:21:"kboard-attr-category1";s:12:"close_button";s:3:"yes";s:10:"field_type";s:9:"category1";s:11:"field_label";s:13:"카테고리1";s:10:"field_name";s:0:"";s:8:"meta_key";s:9:"category1";s:10:"permission";s:3:"all";s:5:"roles";a:1:{i:0;s:13:"administrator";}s:11:"description";s:0:"";s:8:"required";s:0:"";}s:9:"category2";a:10:{s:5:"class";s:21:"kboard-attr-category2";s:12:"close_button";s:3:"yes";s:10:"field_type";s:9:"category2";s:11:"field_label";s:13:"카테고리2";s:10:"field_name";s:0:"";s:8:"meta_key";s:9:"category2";s:10:"permission";s:3:"all";s:5:"roles";a:1:{i:0;s:13:"administrator";}s:11:"description";s:0:"";s:8:"required";s:0:"";}s:13:"tree_category";a:10:{s:5:"class";s:25:"kboard-attr-tree-category";s:12:"close_button";s:3:"yes";s:10:"field_type";s:13:"tree_category";s:11:"field_label";s:22:"계층형 카테고리";s:12:"option_field";s:1:"1";s:10:"field_name";s:0:"";s:8:"meta_key";s:13:"tree_category";s:10:"permission";s:3:"all";s:5:"roles";a:1:{i:0;s:13:"administrator";}s:11:"description";s:0:"";}s:7:"captcha";a:6:{s:5:"class";s:19:"kboard-attr-captcha";s:12:"close_button";s:3:"yes";s:10:"field_type";s:7:"captcha";s:11:"field_label";s:21:"캡차 (보안코드)";s:8:"meta_key";s:7:"captcha";s:11:"description";s:0:"";}s:7:"content";a:9:{s:5:"class";s:19:"kboard-attr-content";s:12:"close_button";s:3:"yes";s:10:"field_type";s:7:"content";s:11:"field_label";s:6:"내용";s:10:"field_name";s:0:"";s:8:"meta_key";s:7:"content";s:11:"placeholder";s:0:"";s:11:"description";s:0:"";s:8:"required";s:0:"";}s:5:"media";a:9:{s:5:"class";s:17:"kboard-attr-media";s:12:"close_button";s:3:"yes";s:10:"field_type";s:5:"media";s:11:"field_label";s:6:"사진";s:10:"field_name";s:0:"";s:8:"meta_key";s:5:"media";s:10:"permission";s:3:"all";s:5:"roles";a:1:{i:0;s:13:"administrator";}s:11:"description";s:0:"";}s:9:"thumbnail";a:9:{s:5:"class";s:21:"kboard-attr-thumbnail";s:12:"close_button";s:3:"yes";s:10:"field_type";s:9:"thumbnail";s:11:"field_label";s:9:"썸네일";s:10:"field_name";s:0:"";s:8:"meta_key";s:9:"thumbnail";s:10:"permission";s:3:"all";s:5:"roles";a:1:{i:0;s:13:"administrator";}s:11:"description";s:0:"";}s:6:"attach";a:9:{s:5:"class";s:18:"kboard-attr-attach";s:12:"close_button";s:3:"yes";s:10:"field_type";s:6:"attach";s:11:"field_label";s:12:"첨부파일";s:10:"field_name";s:0:"";s:8:"meta_key";s:6:"attach";s:10:"permission";s:3:"all";s:5:"roles";a:1:{i:0;s:13:"administrator";}s:11:"description";s:0:"";}s:6:"search";a:11:{s:5:"class";s:18:"kboard-attr-search";s:12:"close_button";s:0:"";s:10:"field_type";s:6:"search";s:11:"field_label";s:12:"통합검색";s:6:"hidden";s:0:"";s:10:"field_name";s:0:"";s:8:"meta_key";s:6:"search";s:10:"permission";s:3:"all";s:5:"roles";a:1:{i:0;s:13:"administrator";}s:13:"default_value";s:1:"1";s:11:"description";s:0:"";}}'
					),
					array('%d', '%s', '%s')
				);
				$wpdb->insert(
					"{$wpdb->prefix}kboard_board_meta",
					array(
						'board_id'       => $board_id,
						'key'			=> 'total',
						'value'			=> '0'
					),
					array('%d', '%s', '%s')
				);
			}
			else{
				$wpdb->update(
					"{$wpdb->prefix}kboard_board_setting",
					array(
						'board_name'       => $board_name,
						'skin'             => $skin,
						'page_rpp'         => $page_rpp,
						'use_comment'      => $use_comment,
						'use_editor'       => $use_editor,
						'permission_read'  => $permission_read,
						'permission_write' => $permission_write,
						'use_category'     => $use_category,
						'category1_list'   => $category1_list,
						'category2_list'   => $category2_list,
						'admin_user'       => $admin_user
					),
					array('uid' => $board_id),
					array('%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),
					array('%d')
				);
			}
			
			$board = new KBoard($board_id);
			
			if($board->id){
				$board->meta->auto_page = $auto_page;
				$board->meta->latest_target_page             = isset($_POST['latest_target_page'])             ? $_POST['latest_target_page']              : '';
				$board->meta->add_menu_page                  = isset($_POST['add_menu_page'])                  ? $_POST['add_menu_page']                   : '';
				$board->meta->notice_invisible_comments      = isset($_POST['notice_invisible_comments'])      ? $_POST['notice_invisible_comments']       : '';
				$board->meta->use_direct_url                 = isset($_POST['use_direct_url'])                 ? $_POST['use_direct_url']                  : '';
				$board->meta->latest_alerts                  = isset($_POST['latest_alerts'])                  ? implode(',', array_map('sanitize_text_field', explode(',', $_POST['latest_alerts']))) : '';
				$board->meta->latest_alerts_attachments_size = isset($_POST['latest_alerts_attachments_size']) ? $_POST['latest_alerts_attachments_size']  : '';
				$board->meta->comment_skin                   = 'default';
				$board->meta->use_tree_category              = isset($_POST['use_tree_category'])              ? $_POST['use_tree_category']               : '';
				$board->meta->default_content                = isset($_POST['default_content'])                ? $_POST['default_content']                 : '';
				$board->meta->pass_autop                     = 'disable';
				$board->meta->shortcode_execute              = isset($_POST['shortcode_execute'])              ? $_POST['shortcode_execute']               : '';
				$board->meta->shortcode_execute_only_admin   = isset($_POST['shortcode_execute_only_admin'])   ? $_POST['shortcode_execute_only_admin']    : '';
				$board->meta->autolink                       = isset($_POST['autolink'])                       ? $_POST['autolink']                        : '';
				$board->meta->reply_copy_content             = isset($_POST['reply_copy_content'])             ? $_POST['reply_copy_content']              : '';
				$board->meta->view_iframe                    = isset($_POST['view_iframe'])                    ? $_POST['view_iframe']                     : '';
				$board->meta->editor_view_iframe             = isset($_POST['editor_view_iframe'])             ? $_POST['editor_view_iframe']              : '';
				$board->meta->permission_list                = isset($_POST['permission_list'])                ? $_POST['permission_list']                 : '';
				$board->meta->permission_access              = isset($_POST['permission_access'])              ? $_POST['permission_access']               : '';
				$board->meta->permission_reply               = isset($_POST['permission_reply'])               ? $_POST['permission_reply']                : '';
				$board->meta->permission_comment_write       = isset($_POST['permission_comment_write'])       ? $_POST['permission_comment_write']        : '';
				$board->meta->permission_comment_read        = isset($_POST['permission_comment_read'])        ? $_POST['permission_comment_read']         : '';
				$board->meta->permission_comment_read_minute = isset($_POST['permission_comment_read_minute']) ? $_POST['permission_comment_read_minute']  : '';
				$board->meta->permission_order               = isset($_POST['permission_order'])               ? $_POST['permission_order']                : '';
				$board->meta->permission_attachment_download = isset($_POST['permission_attachment_download']) ? $_POST['permission_attachment_download']  : '';
				$board->meta->permission_vote                = isset($_POST['permission_vote'])                ? $_POST['permission_vote']                 : '';
				$board->meta->comments_plugin_id             = isset($_POST['comments_plugin_id'])             ? $_POST['comments_plugin_id']              : '';
				$board->meta->use_comments_plugin            = isset($_POST['use_comments_plugin'])            ? $_POST['use_comments_plugin']             : '';
				$board->meta->comments_plugin_row            = isset($_POST['comments_plugin_row'])            ? $_POST['comments_plugin_row']             : '';
				$board->meta->conversion_tracking_code       = isset($_POST['conversion_tracking_code'])       ? $_POST['conversion_tracking_code']        : '';
				$board->meta->always_view_list               = isset($_POST['always_view_list'])               ? $_POST['always_view_list']                : '';
				$board->meta->max_attached_count             = isset($_POST['max_attached_count'])             ? $_POST['max_attached_count']              : '';
				$board->meta->list_sort_numbers              = 'desc';
				$board->meta->permit                         = isset($_POST['permit'])                         ? $_POST['permit']                          : '';
				$board->meta->secret_checked_default         = isset($_POST['secret_checked_default'])         ? $_POST['secret_checked_default']          : '';
				$board->meta->use_prevent_modify_delete      = isset($_POST['use_prevent_modify_delete'])      ? $_POST['use_prevent_modify_delete']       : '';
				$board->meta->max_document_limit             = isset($_POST['max_document_limit'])             ? $_POST['max_document_limit']              : '';
				$board->meta->new_document_delay             = isset($_POST['new_document_delay'])             ? $_POST['new_document_delay']              : '';
				$board->meta->default_build_mod              = isset($_POST['default_build_mod'])              ? $_POST['default_build_mod']               : '';
				$board->meta->after_executing_mod            = isset($_POST['after_executing_mod'])            ? $_POST['after_executing_mod']             : '';
				$board->meta->woocommerce_product_tabs_add   = isset($_POST['woocommerce_product_tabs_add'])   ? $_POST['woocommerce_product_tabs_add']    : '';
				$board->meta->woocommerce_product_tabs_priority = isset($_POST['woocommerce_product_tabs_priority']) ? $_POST['woocommerce_product_tabs_priority'] : '';


				if(isset($_POST['permission_read_roles'])){
					$board->meta->permission_read_roles = serialize($_POST['permission_read_roles']);
				}
				if(isset($_POST['permission_write_roles'])){
					$board->meta->permission_write_roles = serialize($_POST['permission_write_roles']);
				}
				if(isset($_POST['permission_reply_roles'])){
					$board->meta->permission_reply_roles = serialize($_POST['permission_reply_roles']);
				}
				if(isset($_POST['permission_comment_write_roles'])){
					$board->meta->permission_comment_write_roles = serialize($_POST['permission_comment_write_roles']);
				}
				if(isset($_POST['permission_order_roles'])){
					$board->meta->permission_order_roles = serialize($_POST['permission_order_roles']);
				}
				if(isset($_POST['permission_admin_roles'])){
					$board->meta->permission_admin_roles = serialize($_POST['permission_admin_roles']);
				}
				if(isset($_POST['permission_vote_roles'])){
					$board->meta->permission_vote_roles = serialize($_POST['permission_vote_roles']);
				}
				if(isset($_POST['permission_attachment_download_roles'])){
					$board->meta->permission_attachment_download_roles = serialize($_POST['permission_attachment_download_roles']);
				}
				

				$board->meta->skin_fields                    = isset($_POST['fields'])                         ? serialize($_POST['fields'])                   : '';
				$board->meta->document_insert_up_point       = isset($_POST['document_insert_up_point'])       ? abs($_POST['document_insert_up_point'])       : '';
				$board->meta->document_insert_down_point     = isset($_POST['document_insert_down_point'])     ? abs($_POST['document_insert_down_point'])     : '';
				$board->meta->document_delete_up_point       = isset($_POST['document_delete_up_point'])       ? abs($_POST['document_delete_up_point'])       : '';
				$board->meta->document_delete_down_point     = isset($_POST['document_delete_down_point'])     ? abs($_POST['document_delete_down_point'])     : '';
				$board->meta->document_read_down_point       = isset($_POST['document_read_down_point'])       ? abs($_POST['document_read_down_point'])       : '';
				$board->meta->attachment_download_down_point = isset($_POST['attachment_download_down_point']) ? abs($_POST['attachment_download_down_point']) : '';
				$board->meta->comment_insert_up_point        = isset($_POST['comment_insert_up_point'])        ? abs($_POST['comment_insert_up_point'])        : '';
				$board->meta->comment_insert_down_point      = isset($_POST['comment_insert_down_point'])      ? abs($_POST['comment_insert_down_point'])      : '';
				$board->meta->comment_delete_up_point        = isset($_POST['comment_delete_up_point'])        ? abs($_POST['comment_delete_up_point'])        : '';
				$board->meta->comment_delete_down_point      = isset($_POST['comment_delete_down_point'])      ? abs($_POST['comment_delete_down_point'])      : '';
				
				// kboard_extends_setting_update 액션 실행
				do_action('kboard_extends_setting_update', $board->meta, $board_id);
				do_action("kboard_{$board->skin}_extends_setting_update", $board->meta, $board_id);
				
				$tab_kboard_setting = isset($_POST['tab_kboard_setting'])?'#tab-kboard-setting-'.intval($_POST['tab_kboard_setting']):'';
				
			}
			return $board->id;
		}

	}


}

class JDM_moim_resister
{
	public function resister_insert()
	{
		
		if( is_user_logged_in() )
		{
			global $wpdb;
			$current_ID = get_current_user_id();
			$test = $wpdb->insert( "Study_Cafe_List", array(
				'UserID' => $current_ID
			));
			wp_redirect("https://www.ling-on.com/?page_id=1522");
		}
		else
		{
			wp_redirect("https://www.ling-on.com/?page_id=335");
		}
	}
	public function resister_moim()
	{
		global $wpdb;
		$current_ID = get_current_user_id();
		$test = $wpdb->get_row("SELECT * FROM Study_Cafe_List WHERE UserID = $current_ID",OBJECT);
		if($test){
			wp_redirect("https://www.ling-on.com/?page_id=1522");
		}
		else{
			wp_redirect("https://www.ling-on.com/?page_id=814");
		}
	}
}


//현재 moim 등등의 creater가 로그인한 사람인지 테스트하는 함수, 현재의 post id를 입력값으로 받음, 자신이 creater라면 true, 아니라면 false
function OSH_check_moim_creater($current_post_id){
	global $wpdb;
	$post_data = (array)($wpdb->get_row("SELECT * FROM wp_moim WHERE post_id = ".$current_post_id, OBJECT));
	$current_user_ID = get_current_user_id();
		if($post_data['creater']==$current_user_ID){
			return true;
		}else{
			return false;
		}
}

function update_post($form_data){
	global $wpdb;

	if ($form_data[ 'form_id' ] == 4){
		$post_id=2475;

		$form_fields = $form_data[ 'fields' ];
		$description = $form_fields[28]['value'];
		$pnum=$form_fields[31]['value'];
		$start_date = $form_fields[32]['value'];
		$end_date=$form_fields[33]['value'];
		$which_day=$form_fields[36]['value'];
		$link=$form_fields[37]['value'];
		$start_time = $form_fields[35]['value']['hour'].":".$form_fields[35]['value']['minute'].":"."00";
		$time=$form_fields[34]['value'];

		if($description){
			$wpdb->query($wpdb->prepare("UPDATE wp_moim SET description='$description' WHERE post_id=$post_id"));
		}
		if($pnum){
			$wpdb->query($wpdb->prepare("UPDATE wp_moim SET pnum='$pnum' WHERE post_id=$post_id"));
		}
		if($start_date){
			$wpdb->query($wpdb->prepare("UPDATE wp_moim SET start_date='$start_date' WHERE post_id=$post_id"));
		}
		if($end_date){
			$wpdb->query($wpdb->prepare("UPDATE wp_moim SET end_date='$end_date' WHERE post_id=$post_id"));
		}
		if($which_day){
			$wpdb->query($wpdb->prepare("UPDATE wp_moim SET which_day='$which_day' WHERE post_id=$post_id"));
		}
		if($link){
			$wpdb->query($wpdb->prepare("UPDATE wp_moim SET link='$link' WHERE post_id=$post_id"));
		}
		if($start_time){
			$wpdb->query($wpdb->prepare("UPDATE wp_moim SET start_time='$start_time' WHERE post_id=$post_id"));
		}
		if($time){
			$wpdb->query($wpdb->prepare("UPDATE wp_moim SET time='$time' WHERE post_id=$post_id"));
		}

		// $dataset=array(
		// 	'description' => $form_fields[28]['value'],
		// 	'pnum'=>$form_fields[31]['value'],
		// 	'start_date' => $form_fields[32]['value'],
		// 	'end_date'=>$form_fields[33]['value'],
		// 	'which_day'=>$form_fields[36]['value'],
		// 	'link'=>$form_fields[37]['value'],
		// 	'start_time' => $form_fields[35]['value']['hour'].":".$form_fields[35]['value']['minute'].":"."00",
		// 	'time'=>$form_fields[34]['value']
		// );

		// // foreach($dataset as $key=>$value){
		// // 	if($dataset[$key]){
		// // 		$wpdb->query($wpdb->prepare("UPDATE wp_moim SET".$key."='$value' WHERE post_id=$post_id"));
		// // 	}
		// // }


		// $wpdb->query($wpdb->prepare("UPDATE wp_moim SET description='$description', start_date='$start_date', end_date='$end_date', which_day='$which_day', link='$link', start_time='$start_time', time='$time', pnum='$pnum' WHERE post_id=$post_id"));


	}
}