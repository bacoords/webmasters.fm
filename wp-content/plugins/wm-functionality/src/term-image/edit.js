import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { PanelBody, Placeholder, SelectControl } from '@wordpress/components';
import './editor.scss';

export default function Edit( { attributes, setAttributes, context } ) {
	const { display = 'image' } = attributes;
	const postId = context.postId;
	const terms = useSelect(
		( select ) =>
			postId
				? select( 'core' ).getEntityRecords( 'taxonomy', 'speaker', {
						per_page: -1,
						post: postId,
				  } )
				: null,
		[ postId ]
	);
	const shownTerms =
		display === 'text'
			? terms?.filter( ( term ) => term.id !== 14 )
			: terms;
	let preview;
	if ( display === 'text' ) {
		preview = shownTerms?.map( ( term ) => (
			<a
				href={ term.link }
				key={ term.id }
				onClick={ ( event ) => event.preventDefault() }
			>
				{ term.name }
			</a>
		) );
	} else {
		preview = shownTerms
			?.filter( ( term ) => term.image?.url )
			.map( ( term ) => (
				<img src={ term.image.url } alt={ term.name } key={ term.id } />
			) );
	}

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Guest display', 'wm-functionality' ) }>
					<SelectControl
						label={ __( 'Show guests as', 'wm-functionality' ) }
						value={ display }
						options={ [
							{
								label: __( 'Images', 'wm-functionality' ),
								value: 'image',
							},
							{
								label: __( 'Guest names', 'wm-functionality' ),
								value: 'text',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { display: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div
				{ ...useBlockProps( { className: `is-display-${ display }` } ) }
			>
				{ preview?.length ? (
					preview
				) : (
					<Placeholder>
						{ __( 'No guests assigned.', 'wm-functionality' ) }
					</Placeholder>
				) }
			</div>
		</>
	);
}
