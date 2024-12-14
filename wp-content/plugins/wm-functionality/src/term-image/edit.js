/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";
import { useState, useEffect } from "@wordpress/element";
import { Placeholder } from "@wordpress/components";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit({ context }) {
	const post_id = context.postId;
	const [imageUrl, setImageUrl] = useState(null);

	// Get the 'speaker' terms for the post.
	const terms = useSelect(
		(select) =>
			select("core").getEntityRecords("taxonomy", "speaker", {
				per_page: -1,
				post: post_id,
			}),
		[post_id],
	);

	console.log(terms);

	useEffect(() => {
		// Get the first term from the terms list.
		const term = terms && terms[0];
		if (term) {
			// Get the term meta for the 'speaker_image' key.
			const imageUrl = term.image?.url;
			setImageUrl(imageUrl);
		}
	}, [terms]);

	return (
		<div {...useBlockProps()}>
			{imageUrl ? (
				<img src={imageUrl} alt="Speaker" />
			) : (
				<Placeholder>
					{__("No image or speaker.", "wm-functionality")}
				</Placeholder>
			)}
		</div>
	);
}
