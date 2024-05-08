
var config = {
    config: {
        mixins: {
            'mage/gallery/gallery': {
                'MagicToolbox_Magic360/js/gallery': true
            },
            'Magento_ProductVideo/js/fotorama-add-video-events': {
                'MagicToolbox_Magic360/js/fotorama-add-video-events': true
            },
            'Magento_ConfigurableProduct/js/configurable': {
                'MagicToolbox_Magic360/js/configurable': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'MagicToolbox_Magic360/js/swatch-renderer': true
            },
            /* NOTE: for Magento v2.0.x */
            'Magento_Swatches/js/SwatchRenderer': {
                'MagicToolbox_Magic360/js/swatch-renderer': true
            }
        }
    },
    map: {
        '*': {
            loadPlayer: 'MagicToolbox_Magic360/js/load-player',
            vimeoPlayer: 'https://player.vimeo.com/api/player.js'
        }
    }
};
