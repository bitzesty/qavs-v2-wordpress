import './editor.scss';

const { __ } = wp.i18n; // Import __() from wp.i18n
import { registerBlockType } from '@wordpress/blocks';
import { withSelect } from '@wordpress/data';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl } from '@wordpress/components';

registerBlockType('qavs/featured-awardees', {
  apiVersion: 2,
  title: 'Featured awardees',
  icon: 'megaphone',
  category: 'widgets',
  attributes: {
    categoryID: {
      type: "string"
    }
  },
  edit: withSelect((select) => {
    return {
      posts: select('core').getEntityRecords('postType', 'post'),
    };
  })(({ posts, attributes, setAttributes }) => {
    const blockProps = useBlockProps();

    return (
      <div {...blockProps}>
        <h3>Featured awardees configuration</h3>
        <TextControl
          label={__('Category ID')}
          value={attributes.categoryID}
          onChange={(val) => setAttributes({ categoryID: val })}
        />
      </div>
    );
  }),
  save: () => {
    return null;
  }
});
