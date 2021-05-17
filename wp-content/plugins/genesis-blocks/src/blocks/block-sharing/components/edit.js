/**
 * Internal dependencies
 */
import Inspector from './inspector';
import ShareLinks from './sharing';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { AlignmentToolbar, BlockControls } = wp.blockEditor;

export default class Edit extends Component {
	render() {
		return [
			// Show the alignment toolbar on focus
			<BlockControls key="controls">
				<AlignmentToolbar
					value={ this.props.attributes.shareAlignment }
					onChange={ ( value ) =>
						this.props.setAttributes( { shareAlignment: value } )
					}
				/>
			</BlockControls>,

			// Show the block controls on focus
			<Inspector key={ 'gb-share-inspector-' + this.props.clientId } { ...this.props } />,

			// Show the button markup in the editor
			<ShareLinks key={ 'gb-share-links-' + this.props.clientId } { ...this.props }>
				<ul className="gb-share-list">
					{ this.props.attributes.twitter && (
						<li>
							<a className="gb-share-twitter">
								<i className="fab fa-twitter"></i>
								<span className={ 'gb-social-text' }>
									{ __(
										'Share on Twitter',
										'genesis-blocks'
									) }
								</span>
							</a>
						</li>
					) }

					{ this.props.attributes.facebook && (
						<li>
							<a className="gb-share-facebook">
								<i className="fab fa-facebook-f"></i>
								<span className={ 'gb-social-text' }>
									{ __(
										'Share on Facebook',
										'genesis-blocks'
									) }
								</span>
							</a>
						</li>
					) }

					{ this.props.attributes.pinterest && (
						<li>
							<a className="gb-share-pinterest">
								<i className="fab fa-pinterest-p"></i>
								<span className={ 'gb-social-text' }>
									{ __(
										'Share on Pinterest',
										'genesis-blocks'
									) }
								</span>
							</a>
						</li>
					) }

					{ this.props.attributes.linkedin && (
						<li>
							<a className="gb-share-linkedin">
								<i className="fab fa-linkedin"></i>
								<span className={ 'gb-social-text' }>
									{ __(
										'Share on LinkedIn',
										'genesis-blocks'
									) }
								</span>
							</a>
						</li>
					) }

					{ this.props.attributes.reddit && (
						<li>
							<a className="gb-share-reddit">
								<i className="fab fa-reddit-alien"></i>
								<span className={ 'gb-social-text' }>
									{ __( 'Share on reddit', 'genesis-blocks' ) }
								</span>
							</a>
						</li>
					) }

					{ this.props.attributes.email && (
						<li>
							<a className="gb-share-email">
								<i className="fas fa-envelope"></i>
								<span className={ 'gb-social-text' }>
									{ __( 'Share via Email', 'genesis-blocks' ) }
								</span>
							</a>
						</li>
					) }
				</ul>
			</ShareLinks>,
		];
	}
}
