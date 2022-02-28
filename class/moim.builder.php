<?php

class JDM_Moim_Builder
{
	var $now_post_id;
	var $title;
	var $description;
	var $pnum;
	var $which_day;
	var $time;
	var $kboardID;
	var $link;
	var $test = "OK";
	private $now_info_, $row, $now_post_id_;

	public function __construct()
	{
		global $wpdb;
		$this->now_post_id = get_the_ID();
		$now_post_id_ = $this->now_post_id; //??
		//error_log($now_post_id_);
		$now_info_ = $wpdb->get_row("SELECT * FROM wp_moim WHERE post_id = ".$now_post_id_, OBJECT);
		$row = (array)$now_info_;
		$this->title = $row['title'];
		$this->stars = 0;
		$this->description = $row['description'];
		$this->pnum = $row['pnum'];
		$this->which_day = $row['which_day'];
		$this->time = $row['time'];
		$this->kboardID = $row['kboard_id'];
		$this->link = $row['link'];

	}
	public function moim_title()
	{
		ob_start();
		echo('<font size=7 color="#FFFFFF">'.$this->title.'</font>');
		return ob_get_clean();
	}
	public function moim_stars()
	{
		$now_post_id_ = $this->now_post_id;
		do_shortcode('[site_reviews_summary assigned_posts='.$now_post_id_.' hide="bars, if_empty, rating, summary"]');
	}
	public function moim_description()
	{
		ob_start();
		echo('<font size=5 color="#FFFFFF">'.strval($this->description).'</font>');
		return ob_get_clean();
	}
	public function moim_info()
	{
		ob_start();
		echo('<font size=4 color="#FFFFFF">'.'참여 인원 : '.strval($this->pnum).'</font>');
		return ob_get_clean();
	}
	public function moim_schedule()
	{
		ob_start();
		echo('<font size=4 color="#FFFFFF">'.'요일 : '.strval($this->which_day).'<br>시간 : '.strval($this->time).'</font>');
		return ob_get_clean();
	}
	public function moim_link()
	{
		ob_start();
		echo('<div class="elementor-section-wrap">
		<section class="elementor-section elementor-top-section elementor-element elementor-element-816b559 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="816b559" data-element_type="section">
	<div class="elementor-container elementor-column-gap-default">
<div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-6783976" data-id="6783976" data-element_type="column">
<div class="elementor-widget-wrap elementor-element-populated">
			<div class="elementor-element elementor-element-e5ff4a0 elementor-align-center elementor-widget elementor-widget-button" data-id="e5ff4a0" data-element_type="widget" data-widget_type="button.default">
<div class="elementor-widget-container">
<div class="elementor-button-wrapper">
<a href="'.strval($this->link).'" class="elementor-button-link elementor-button elementor-size-xl" role="button">
	<span class="elementor-button-content-wrapper">
	<span class="elementor-button-text">참여하기</span>
</span>
</a>
</div>
</div>
</div>
</div>
</div>
		</div>
</section>');
		return ob_get_clean();
		
	}

	public function moim_update(){
		if(OSH_check_moim_creater($this->now_post_id)){//자신이 creater라면 수정 버튼이 나오고, 아니라면 나오지 않는다
			ob_start();
			echo('<div class="elementor-section-wrap">
			<section class="elementor-section elementor-top-section elementor-element elementor-element-816b559 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="816b559" data-element_type="section">
		<div class="elementor-container elementor-column-gap-default">
	<div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-6783976" data-id="6783976" data-element_type="column">
	<div class="elementor-widget-wrap elementor-element-populated">
				<div class="elementor-element elementor-element-e5ff4a0 elementor-align-center elementor-widget elementor-widget-button" data-id="e5ff4a0" data-element_type="widget" data-widget_type="button.default">
	<div class="elementor-widget-container">
	<div class="elementor-button-wrapper">
	<a href="https://www.ling-on.com/?page_id=2512" class="elementor-button-link elementor-button elementor-size-s" role="button">
		<span class="elementor-button-content-wrapper">
		<span class="elementor-button-text">수정</span>
	</span>
	</a>
	</div>
	</div>
	</div>
	</div>
	</div>
			</div>
	</section>');
			return ob_get_clean();
		}
		}


}

function Moim_Builder( $content )
{
	$now_post_type = get_post_type();
	if ($now_post_type == 'moim_page')
	{
		$MoimBuilder = new JDM_Moim_Builder();
		$content = '';
		$content = $content.add_shortcode('lingon_moim_title', array($MoimBuilder, 'moim_title'));
		$content = $content.add_shortcode('lingon_moim_stars', array($MoimBuilder, 'moim_stars'));
		$content = $content.add_shortcode('lingon_moim_description', array($MoimBuilder, 'moim_description'));
		$content = $content.add_shortcode('lingon_moim_info', array($MoimBuilder, 'moim_info'));
		$content = $content.add_shortcode('lingon_moim_schedule', array($MoimBuilder, 'moim_schedule'));
		$content = $content.add_shortcode('lingon_moim_link', array($MoimBuilder, 'moim_link'));

		$content=$content.add_shortcode('lingon_update_link',array($MoimBuilder,'moim_update'));

		$content = $content.do_shortcode('[oceanwp_library id="1485"]');
		$content = $content.do_shortcode('[oceanwp_library id="1500"]');
		$content = $content.do_shortcode('[oceanwp_library id="1504"]');
		$content = $content.do_shortcode('[kboard id='.$MoimBuilder->kboardID.']');
		$content = $content.do_shortcode('[site_reviews_form assigned_posts='.$MoimBuilder->now_post_id.']');
		return $content;
	}
	else
	{
		$content;
	}
	
}