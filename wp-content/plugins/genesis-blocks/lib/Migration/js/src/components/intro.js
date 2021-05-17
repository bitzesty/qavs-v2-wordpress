// @ts-check
/* global genesisBlocksMigration */
/**
 * External dependencies
 */
import * as React from 'react';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * The introduction to the migration.
 *
 * @return {React.ReactElement} The introduction to the migration.
 */
const Intro = () => {
	const developerNoticeUrl = 'https://wpeng.in/ab-gb-dev/';

	let headerText = __( 'Atomic Blocks has been renamed to Genesis Blocks', 'genesis-blocks' );
	if ( genesisBlocksMigration.isPro ) {
		headerText = __( 'We need to update your blocks to give you the latest features!', 'genesis-blocks' );
	}

	return (
		<>
			<div>
				<h2>{ headerText }</h2>
				<p>{ __( 'Same powerful blocks, same beautiful designs, same innovative team.', 'genesis-blocks' ) }</p>
				<p>{ __( 'To continue receiving the best of what our team is building, we encourage you to migrate. Our migration tool makes this nice and easy, and for the majority of use cases, completely automated.', 'genesis-blocks' ) }</p>
				<div className="dev-notice">
					<svg fill="currentColor" viewBox="0 0 20 20">
						<path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd"></path>
					</svg>
					<span>{ __( 'Need to let the developer for this site know about this? Send them this link.', 'genesis-blocks' ) }</span>
					<a href={ developerNoticeUrl } target="_blank" rel="noopener noreferrer" className="btn">
						<span>{ __( 'Developer Notice', 'genesis-blocks' ) }</span>
						<svg fill="currentColor" viewBox="0 0 20 20">
							<path d="M11 3a1 1 0 100 2h3.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"></path>
							<path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"></path>
						</svg>
					</a>
				</div>
			</div>
			<h2>{ __( "Let’s Migrate", 'genesis-blocks' ) }</h2>
		</>
	);
};

export default Intro;
