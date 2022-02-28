<?php
//로그인 된 상태를 판별하고 헤더(사이트 상단 메뉴바)를 다르게 표시해주는 기능 추가
class JDM_Login{
	//is_user_logged_in() : 워드프레스 내장 함수, 유저가 로그인 된 상태일 시 true, 그렇지 않을 시 false 반환
	//do_shortcode : shortcode를 실행하라는 명령을 내리는 함수, 사이트에 elementor 편집기로 넣어 실행할 수 도 있고 php에서 do_shortcode로 실행할 수 도 있다.
	//[oceanwp_library id=""] : 사이트의 최소 단위인 라이브러리를 표시해주는 숏코드
	private $Mainlog;
	public function LoginHeader_Displayer_JDM(){
		if ( wp_is_mobile() ) {
			$Mainlog = do_shortcode('[oceanwp_library id="2764"]');
		}
		else{
			if ( is_user_logged_in() ) {
				$Mainlog = do_shortcode('[oceanwp_library id="1429"]');
			}
			else {
				$Mainlog = do_shortcode('[oceanwp_library id="1201"]');
			}
		}
		return $Mainlog;
	}
}