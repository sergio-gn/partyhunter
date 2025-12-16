<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php wp_head(); ?>
    <?php $gtag = get_field('google_tag','option'); if (isset($gtag) && !empty($gtag)){echo $gtag;}?>
</head>

<?php 
    $websitePalette = get_field('website_palette','option');
    if($websitePalette):
?>
<style>
    :root{
    --main_colour: <?php echo $websitePalette['main_colour'] ?>;
    --main_colour_darker: <?php echo $websitePalette['main_colour_darker'] ?>;
    --secondary_colour: <?php echo $websitePalette['secondary_colour'] ?>;
    --background_colour: <?php echo $websitePalette['background_colour'] ?>;
    --navigation_bar_background_colour: <?php echo $websitePalette['navigation_bar_background_colour'] ?>;
    --footer_background_colour: <?php echo $websitePalette['footer_background_colour'] ?>;
    --font_gradient_left: <?php echo $websitePalette['font_gradient_colour_1'] ?>;
    --font_gradient_right: <?php echo $websitePalette['font_gradient_colour_2'] ?>;
    --white_tone:<?php echo $websitePalette['white_tone'] ?>;
    --white_tone_darker: <?php echo $websitePalette['white_tone_darker'] ?>;
    --black_tone: <?php echo $websitePalette['black_tone'] ?>;
    --grey_tone: <?php echo $websitePalette['grey_tone'] ?>;
    --light_grey_tone: <?php echo $websitePalette['light_grey_tone'] ?>;
    }
</style>
<?php endif;?>