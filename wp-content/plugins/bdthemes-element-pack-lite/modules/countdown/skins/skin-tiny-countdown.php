<?php
namespace ElementPack\Modules\Countdown\Skins;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Schemes;
use Elementor\Utils;
use Elementor\Schemes\Color;

use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Tiny_Countdown extends Elementor_Skin_Base {
	
	public function get_id() {
		return 'bdt-tiny-countdown';
	}

	public function get_title() {
		return __( 'Tiny Countdown', 'bdthemes-element-pack' );
    }
    
    public function render() {
		$settings      = $this->parent->get_settings_for_display();
		$due_date      = $settings['due_date'];
		$string        = $this->parent->get_strftime( $settings );
		
		$with_gmt_time = date( 'Y-m-d H:i', strtotime( $due_date ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );		
		$datetime      = new \DateTime($with_gmt_time);
		$final_time    = $datetime->format('c');

		$this->parent->add_render_attribute(
			[
				'countdown' => [
					'bdt-countdown' => [
						'date: ' . $final_time,
					],
				],
			]
		);

		?>
		<div class="bdt-countdown-skin-tiny">
			<div <?php echo $this->parent->get_render_attribute_string( 'countdown' ); ?>>
				<?php echo wp_kses_post($string); ?>
			</div>
		</div>
		<?php
	}

}

