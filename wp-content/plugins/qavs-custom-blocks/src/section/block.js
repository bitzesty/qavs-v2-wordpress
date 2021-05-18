/**
 * BLOCK: suncsn-section-block
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './style.scss';
import './editor.scss';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const { InnerBlocks } = wp.editor; // Import registerBlockType() from wp.blocks
/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType('qavs/section', {
  // Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
  title: __('Section'), // Block title.
  icon: 'shield', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
  category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
  keywords: [],
  styles: [
    {
      name: 'default',
      label: __('Gray'),
      isDefault: true
    },
    {
      name: 'white',
      label: __('White')
    }
  ],
  edit: function (props) {
    let contents = ('undefined' !== typeof props.insertBlocksAfter) ? <InnerBlocks /> : '';

    return (
      <div className={'section ' + props.className}>
        <div className="container">
          {contents}
        </div>
      </div>
    );
  },
  save: function (props) {
    return (
      <div className={'section ' + props.className}>
        <div className="container">
          <InnerBlocks.Content />
        </div>
      </div>
    );
  },
});
