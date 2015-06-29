<?php
/*
Template Name: Distributors Page
*/
get_header(); 

// Custom css file to handle google maps display
	//wp_enqueue_style( 'gmaps', get_stylesheet_directory_uri() . '/library/css/google_maps.css' );

	

	// Google Maps API is dependency for gmaps script called above
	// Gmaps must be called AFTER the API, so we run the API as the dependency enqueued after gmaps

	// format to call Google Maps API
	// But with our correct API Key (for OrgSpring Use Only)
	wp_enqueue_script( 'gmapsapi', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDkTLVaP1U3JBrrHGRoaL-oUbsHe4VANa4', array(), '1.0.0', true );

	// Custom script to handle rendering map, placement of markers, and html info windows
	//wp_enqueue_script( 'gmaps', get_stylesheet_directory_uri() . '/library/js/acfmap.js', array( 'gmapsapi' ), '1.0.0', false );
?>

<script type="text/javascript">
  (function($) {

  /*
  *  render_map
  *
  *  This function will render a Google Map onto the selected jQuery element
  *
  *  @type  function
  *  @date  8/11/2013
  *  @since 4.3.0
  *
  *  @param $el (jQuery element)
  *  @return  n/a
  */

  var $window = $(window);
   
  function render_map( $el ) {

    // var
    var $markers = $el.find('.marker');

    if ($window.width() > 1001) {
      var mmzoom = 2;
    }

    if ($window.width() < 1000 ) {
      var mmzoom = 1;
    }
    // vars
    var args = {
      zoom    : mmzoom,
      center    : new google.maps.LatLng(20, 10),
      zoomControl: true,
      mapTypeId : google.maps.MapTypeId.ROADMAP
    };

    // create map           
    var map = new google.maps.Map( $el[0], args);

    // add a markers reference
    map.markers = [];



    // add markers
    $markers.each(function(){

        add_marker( $(this), map );

    });

    // center map
    //center_map( map );

  }

  /*
  *  add_marker
  *
  *  This function will add a marker to the selected Google Map
  *
  *  @type  function
  *  @date  8/11/2013
  *  @since 4.3.0
  *
  *  @param $marker (jQuery element)
  *  @param map (Google Map object)
  *  @return  n/a
  */

  function add_marker( $marker, map ) {

    // var
    var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );

    // create marker
    var marker = new google.maps.Marker({
      position  : latlng,
      map     : map
    });

    // add to array
    map.markers.push( marker );

    // if marker contains HTML, add it to an infoWindow
    if( $marker.html() )
    {
      // create info window
      var infowindow = new google.maps.InfoWindow({
        content   : $marker.html()
      });

      // show info window when marker is clicked
      google.maps.event.addListener(marker, 'click', function() {

        infowindow.open( map, marker );

      });
    }

  }

  /*
  *  center_map
  *
  *  This function will center the map, showing all markers attached to this map
  *
  *  @type  function
  *  @date  8/11/2013
  *  @since 4.3.0
  *
  *  @param map (Google Map object)
  *  @return  n/a
  */

  /*function center_map( map ) {

    // vars
    var bounds = new google.maps.LatLngBounds();

    // loop through all markers and create bounds
    $.each( map.markers, function( i, marker ){

      var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );
      var worldBounds = new google.maps.LatLngBounds(new google.maps.LatLng(-75,-180),
                                               new google.maps.LatLng(75,180));
      bounds.extend( worldBounds );

    });

    // only 1 marker?
    if( map.markers.length == 1 )
    {
      // set center of map
        map.setCenter( bounds.getCenter() );
        map.setZoom( 6 );
    }
    else
    {
      // fit to bounds
      map.fitBounds( bounds );
    }

  }*/

  /*
  *  document ready
  *
  *  This function will render each map when the document is ready (page has loaded)
  *
  *  @type  function
  *  @date  8/11/2013
  *  @since 5.0.0
  *
  *  @param n/a
  *  @return  n/a
  */

  $(document).ready(function(){

    $('.acf-map').each(function(){

      render_map( $(this) );

    });


  });

  })(jQuery);
</script>

	<div id="content">

		<div id="inner-content" class="wrap cf">
				
				<div id="main" class="main-content cf" role="main">
					<?php if (function_exists('get_field') && get_field('banner_image')) {
						//echo '<img class="banner-image" src="' . get_field('banner_image') . '" />';
					} ?>
					<?php if ( function_exists('yoast_breadcrumb') ) {
						yoast_breadcrumb('<p id="breadcrumbs">','</p>');
					} 

					$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); 

					?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

						<header class="article-header">

							<h1 class="page-title" itemprop="headline">Photron Distributors for <?php echo $term->name ?></h1>

						</header> <?php // end article header ?>

						<section class="entry-content cf" itemprop="articleBody">
							<?php 
							
							$maincontent = get_the_content();

							$args = array(
								        'post_type' => 'distributor',
								        'post_status' => 'publish',
								        'posts_per_page' => -1,
								        'tax_query' => array(
								        	array(
								        		'taxonomy' => 'distributor_country',
								        		'field' => 'slug',
								        		'terms' => $term->slug,
								        	)
								        ),
								     );

							$maploop = new WP_Query($args);


							if($maploop->have_posts()) {
								echo '<div class="acf-map">';
								  while($maploop->have_posts()) {
									$maploop->the_post();


								//	Get location coordinates from google maps field in ACF
								$maplocation = get_field( 'map_address' );
								if(function_exists('get_field') && get_field('email')) { $email = get_field('email'); }
								?>

								<div class="marker" style="display:none;" data-lat="<?php echo $maplocation['lat']; ?>" data-lng="<?php echo $maplocation[ 'lng' ]; ?>" data-icon="<?php echo get_stylesheet_directory_uri() . '/library/images/favicon-32x32.png'; ?>">
									<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
						 			<?php if (function_exists('get_field') && get_field('phone') ) { ?><div class="addl-data"><span>Phone: </span><?php the_field('phone'); ?></div><?php } ?>
						 			<?php if ( $email ) { ?><div class="addl-data"><span><a href="mailto:<?php echo antispambot($email); ?>" target="_blank">Email</a></span></div><?php } ?>
						 			<?php if (function_exists('get_field') && get_field('url') ) { ?><div class="addl-data"><span><a href="<?php the_field('url'); ?>" target="_blank">Website</a></span></div><?php } ?>
									<!-- <div class="location-image"><img src="<?php echo get_stylesheet_directory_uri() . '/library/images/favicon-32x32.png'; ?>" /></div> -->
						 
									<!-- <p><?php echo $maplocation['address']; ?></p> -->
								</div>
							<?php } //endwhile maploop #1

								echo '</div><!-- .acfmap -->';
								 rewind_posts();

								 echo '<div class="page-content">' . $maincontent . '</div>';
								 echo '<div class="cf distributors-list">';
                      // SHOW US STATE DROPDOWN IF US 
                        if ( $term->slug == 'united-states') {
                          echo '<div class="state-dropdown">';
                            echo get_terms_dropdown('distributor_state', array('hide_empty=0'), 'Choose your state');
                          echo '</div>';
                        }

								  while($maploop->have_posts()) {
									$maploop->the_post();
                    
                    if ( $term->slug == 'united-states') {
                      $custom_states = get_the_terms($maploop->ID, 'distributor_state');
                      if ( $custom_states && !is_wp_error($custom_states)) {
                            echo '<div style="display:none;" class="js-box distrib-deet ';
                            foreach ($custom_states as $custom_state) {
                              echo $custom_state->slug . ' ';
                            }
                            echo '">';
                          }
                    } else {
                      echo '<div class="distrib-deet">';
                    }
								    $maplocation = get_field( 'map_address' );
								        
								    echo '<h3><a href="'.get_permalink().'"><i class="fa fa-link"></i> '.get_the_title().'</a></h3>'; ?>

								    <?php if (function_exists('get_field') && get_field('phone')) { ?>
                      <span class="distrib-info"><h6>Phone:</h6><?php the_field('phone'); ?><?php if (get_field('phone_2')) { echo ', ' . get_field('phone_2'); } ?></span>
                    <?php } ?>
                    <?php if (function_exists('get_field') && get_field('fax')) { ?>
                      <span class="distrib-info"><h6>Fax:</h6><?php the_field('fax'); ?></span>
                    <?php } ?>
                    <?php if (function_exists('get_field') && get_field('url')) { ?>
                      <span class="distrib-info"><h6>Website:</h6><a href="<?php the_field('url'); ?>" target="_blank"><?php the_field('url'); ?></a></span>
                    <?php } ?>
                    <?php if (function_exists('get_field') && get_field('email')) { ?>
                      <?php $email = get_field('email'); ?>
                      <span class="distrib-info"><h6>Email:</h6><a href="mailto:<?php echo antispambot($email); ?>" target="_blank"><?php echo antispambot($email); ?></a></span>
                    <?php } 
                    echo '</div>'; // distrib-deet
                     }
								
								echo '</div>'; // distributors-list

							} // endif main maploop

							?>
						</section> <?php // end article section ?>

					</article>

					

					

				</div>

				<?php  get_sidebar(); ?>

		</div>

	</div>

<?php get_footer(); ?>