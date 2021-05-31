/**
 * BLOCK: qavs-custom-blocks
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './editor.scss';
import './style.scss';

const { __ } = wp.i18n; // Import __() from wp.i18n
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { TextControl, ExternalLink } from '@wordpress/components';

const renderExpander = (attributes, className) => {
  const blockProps = useBlockProps.save();
  
  return (
    <div className={`expander ${className}`}>
      <button className='expander__heading' ariaExpanded={false}><span>{attributes.heading}</span></button>
      <div className="expander__content">
        <RichText.Content {...blockProps} tagName="div" value={attributes.content} />
      </div>
    </div>
  );
};

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
registerBlockType( 'qavs/expander', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'Expander' ), // Block title.
	icon: 'megaphone', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
  keywords: [],
  attributes: {
    heading: {
      type: 'string',
      source: 'text',
      selector: '.expander__heading span',
    },
    content: {
      type: 'string',
      source: 'html',
      selector: '.expander__content',
    }
  },

	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Component.
	 */
  edit: ({ className, attributes, setAttributes, isSelected }) => {
    if (!isSelected) {
      return renderExpander(attributes, className);
    }

    const blockProps = useBlockProps();

    return (
      <div>
        <TextControl
          label={__('Heading')}
          value={attributes.heading}
          onChange={(val) => setAttributes({ heading: val })}
        />
        <label>{__('Resource text')}</label>
        <div className="rich-text-wrapper">
          <RichText
            {...blockProps}
            tagName="div"
            label={__('Resource text')}
            value={attributes.content}
            onChange={(val) => setAttributes({ content: val })}
          />
        </div>
      </div>
    )
  },

	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Frontend HTML.
	 */
  save: ({ className, attributes }) => {
		return renderExpander(attributes, className);
	},
} );


