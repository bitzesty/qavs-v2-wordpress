<?php
/**
 * Genesis Blocks Blog Posts section for Slate Collection.
 *
 * @package genesis-blocks
 */

return [
	'type'       => 'section',
	'key'        => 'gb_slate_section_blog_posts',
	'collection' => [
		'slug'  => 'slate',
		'label' => __( 'Slate', 'genesis-blocks' ),
	],
	'content'    => "<!-- wp:genesis-blocks/gb-columns {\"columns\":1,\"layout\":\"gb-1-col-equal\",\"align\":\"full\",\"paddingTop\":6,\"paddingRight\":1,\"paddingBottom\":3,\"paddingLeft\":1,\"paddingUnit\":\"em\",\"customBackgroundColor\":\"#ededed\",\"columnMaxWidth\":1200,\"className\":\"gpb-slate-section-blog-posts\"} --> <div class=\"wp-block-genesis-blocks-gb-columns gpb-slate-section-blog-posts gb-layout-columns-1 gb-1-col-equal gb-has-custom-background-color gb-columns-center alignfull\" style=\"padding-top:6em;padding-right:1em;padding-bottom:3em;padding-left:1em;background-color:#ededed\"><div class=\"gb-layout-column-wrap gb-block-layout-column-gap-2 gb-is-responsive-column\" style=\"max-width:1200px\"><!-- wp:genesis-blocks/gb-column --> <div class=\"wp-block-genesis-blocks-gb-column gb-block-layout-column\"><div class=\"gb-block-layout-column-inner\"><!-- wp:genesis-blocks/gb-post-grid {\"postsToShow\":3,\"columns\":3,\"imageSize\":\"medium\"} /--></div></div> <!-- /wp:genesis-blocks/gb-column --></div></div> <!-- /wp:genesis-blocks/gb-columns -->",
	'name'       => esc_html__( 'Slate Blog Posts', 'genesis-blocks' ),
	'category'   => [
		esc_html__( 'blog', 'genesis-blocks' ),
		esc_html__( 'posts', 'genesis-blocks' ),
	],
	'keywords'   => [
		esc_html__( 'slate', 'genesis-blocks' ),
		esc_html__( 'blog', 'genesis-blocks' ),
		esc_html__( 'posts', 'genesis-blocks' ),
		esc_html__( 'slate blog posts', 'genesis-blocks' ),
	],
	'image'      => 'https://demo.studiopress.com/page-builder/slate/gb_slate_section_blog_posts.jpg',
];
