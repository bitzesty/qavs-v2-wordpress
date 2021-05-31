const { __ } = wp.i18n; // Import __() from wp.i18n
import { registerBlockType } from '@wordpress/blocks';
import { withSelect } from '@wordpress/data';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl } from '@wordpress/components';

registerBlockType('qavs/parental-tabs', {
  apiVersion: 2,
  title: 'Parental tabs',
  icon: 'megaphone',
  category: 'widgets',
  attributes: {
    categoryID: {
      type: "string"
    }
  },
  edit: ({ attributes, setAttributes }) => {
    const blockProps = useBlockProps();

    return (
      <div {...blockProps}>
        <h3>Tabs based on parent page</h3>
      </div>
    );
  },
  save: () => {
    return null;
  }
});
