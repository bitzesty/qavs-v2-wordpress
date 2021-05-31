const { __ } = wp.i18n; // Import __() from wp.i18n
import { registerBlockType } from '@wordpress/blocks';
import { withSelect } from '@wordpress/data';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl } from '@wordpress/components';

registerBlockType('qavs/parental-pagination', {
  apiVersion: 2,
  title: 'Parental pagination',
  icon: 'megaphone',
  category: 'widgets',
  edit: ({ attributes, setAttributes }) => {
    const blockProps = useBlockProps();

    return (
      <div {...blockProps}>
        <h3>Pagination based on parent page</h3>
      </div>
    );
  },
  save: () => {
    return null;
  }
});
