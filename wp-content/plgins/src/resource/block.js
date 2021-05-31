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
import { TextControl, ExternalLink, PanelBody, Button, ResponsiveWrapper } from '@wordpress/components';
const { withSelect } = wp.data;
const { MediaUpload, MediaUploadCheck, InspectorControls } = wp.editor;

const renderResource = (attributes, className) => {
  const blockProps = useBlockProps.save();
  
  return (
    <div className={`resource ${className}`}>
      {attributes.mediaUrl != 0 && <img src={attributes.mediaUrl} alt={attributes.name} className="resource__image" />}
      <div className="resource__details">
        <h3 className="resource__name">{attributes.name}</h3>
        <div className="resource__content">
          <RichText.Content value={attributes.content} />
        </div>
        {attributes.url && <a href={attributes.url} rel='noopener nofollow' aria-label={`Visit ${attributes.name}'s website`} className="resource__cta">Visit website</a>}
      </div>
    </div>
  );
};

const BlockEdit = props => {
  const { attributes, setAttributes, className, isSelected } = props;

  if (!isSelected) {
    return renderResource(attributes, className);
  }

  const blockProps = useBlockProps();

  const onSelectMedia = (media) => {
    props.setAttributes({
      mediaId: media.id,
      mediaUrl: media.url
    });
  }

  const removeMedia = () => {
    props.setAttributes({
      mediaId: 0,
      mediaUrl: ''
    });
  }

  const instructions = (
    <p>{__("Choose an image")}</p>
  );

  return (
    <div className={className}>
      <InspectorControls>
        <PanelBody title={__('Volunteering resource details')} initialOpen={true}>
          <MediaUploadCheck fallback={instructions}>
            <MediaUpload
              title={__('Picture')}
              onSelect={onSelectMedia}
              value={attributes.mediaId}
              allowedTypes={["image"]}
              render={({ open }) => (
                <Button
                  className={attributes.mediaId == 0 ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview'}
                  onClick={open}>
                  {attributes.mediaId == 0 && __('Choose an image')}
                </Button>
              )}
            />
          </MediaUploadCheck>
          {props.media != undefined &&
            <ResponsiveWrapper naturalWidth={props.media.media_details.width} naturalHeight={props.media.media_details.height}>
              <img src={props.media.source_url} />
            </ResponsiveWrapper>
          }
          {attributes.mediaId != 0 &&
            <div>
              <br />
              <MediaUploadCheck>
                <MediaUpload
                  title={__('Replace picture')}
                  value={attributes.mediaId}
                  onSelect={onSelectMedia}
                  allowedTypes={['image']}
                  render={({ open }) => (
                    <Button onClick={open} isDefault isLarge>{__('Replace picture')}</Button>
                  )}
                />
              </MediaUploadCheck>
            </div>
          }
          {attributes.mediaId != 0 &&
            <div>
              <br />
              <MediaUploadCheck>
                <Button onClick={removeMedia} isLink isDestructive>
                  {__('Remove picture')}
                </Button>
              </MediaUploadCheck>
            </div>
          }
        </PanelBody>
      </InspectorControls>

      <TextControl
        label={__('Name')}
        value={attributes.name}
        onChange={(val) => setAttributes({ name: val })}
      />

      <TextControl
        label={__('URL')}
        value={attributes.url}
        onChange={(val) => setAttributes({ url: val })}
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
  );
}

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
registerBlockType( 'qavs/resource', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'Volunteering resource' ), // Block title.
	icon: 'megaphone', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
  keywords: [],
  attributes: {
    name: {
      type: 'string',
      source: 'text',
      selector: '.resource__name',
    },
    content: {
      type: 'string',
      source: 'html',
      selector: '.resource__content',
    },
    url: {
      type: 'string',
      default: ''
    },
    mediaId: {
      type: 'number',
      default: 0
    },
    mediaUrl: {
      type: 'string',
      default: ''
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
  edit: withSelect((select, props) => {
    return { media: props.attributes.mediaId ? select('core').getMedia(props.attributes.mediaId) : undefined };
  })(BlockEdit),
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
		return renderResource(attributes, className);
	},
} );


