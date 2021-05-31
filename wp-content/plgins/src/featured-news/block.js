import './editor.scss';

import { registerBlockType } from '@wordpress/blocks';
import { withSelect } from '@wordpress/data';
import { useBlockProps } from '@wordpress/block-editor';

registerBlockType('qavs/featured-news', {
  apiVersion: 2,
  title: 'Featured news',
  icon: 'megaphone',
  category: 'widgets',

  edit: withSelect((select) => {
    return {
      posts: select('core').getEntityRecords('postType', 'post'),
    };
  })(({ posts }) => {
    const blockProps = useBlockProps();

    return (
      <div {...blockProps}>
        <div className="dynamic-block-placeholder">
          <h3>Featured news</h3>
        </div>
      </div>
    );
  }),
  save: () => {
    return null;
  }
});
