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
	const [imageUrls, setImageUrls] = useState([]);

	// Get the 'speaker' terms for the post.
	const terms = useSelect(
		(select) =>
			select("core").getEntityRecords("taxonomy", "speaker", {
				per_page: -1,
				post: post_id,
			}),
		[post_id],
	);

	useEffect(() => {
		// Get the first term from the terms list.
		if (terms && terms.length > 0) {
			const urls = terms.map((term) => term.image?.url);
			setImageUrls(urls);
		}
	}, [terms]);

	return (
		<div {...useBlockProps()}>
			{imageUrls.length > 0 ? (
				imageUrls.map((imageUrl) => (
					<a href={imageUrl} target="_blank" rel="noopener noreferrer">
						<img
							src={imageUrl}
							alt="Speaker"
							style={{ width: "50px", height: "50px" }}
						/>
					</a>
				))
			) : (
				<Placeholder>
					{__("No image or speaker.", "wm-functionality")}
				</Placeholder>
			)}
		</div>
	);
}
