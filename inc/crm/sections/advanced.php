<?php
$meta = \GigaAI\Storage\Storage::getUserMeta( $lead_id );

if ( ! empty( $meta ) ) :
foreach ( $meta as $key => $value ) :
?>
<div class="form-group">
    <label for="advanced-<?php echo esc_attr( $key ) ?>"><?php echo str_title( esc_attr( $key ) ); ?></label>
    <textarea name="meta_<?php echo esc_attr( $key ) ?>" id="advanced-<?php echo esc_attr( $key ); ?>"
              class="form-control"><?php echo esc_textarea( $value ); ?></textarea>
</div>

<?php endforeach;
else: ?>
<h4><?php _e( 'This lead have not any meta data yet', 'giga-messenger-bots' ); ?></h4>
<?php
endif;