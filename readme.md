# [Shortcode Directives](http://wordpress.org/plugins/shortcode-directives/) #

### Welcome to our GitHub Repository

Shortcode Directives is an open source WordPress plugin that allows you to perform certain actions on posts and comments with shortcode directives.

<p align="center">
    <img src="" alt="Shortcode Directives" />
</p>

## Screenshots ##

<div style="margin:20px; float:left">
	<a href="" title="Shortcode Directives - Settings">
		<img src="" alt="Shortcode Directives - Settings" width="320" />
	</a>
	&nbsp;
</div>

## Installation ##

- The latest development version can be found [here](https://github.com/michaeluno/shortcode-directives/branches). 
- The latest stable version can be downloaded [here](http://downloads.wordpress.org/plugin/shortcode-directives.latest-stable.zip).

1. Upload **`shortcode-directives.php`** and other files compressed in the zip folder to the **`/wp-content/plugins/`** directory.,
2. Activate the plugin through the `Plugins` menu in WordPress.
3. Go to **Dashboard** -> **Tools** -> **Shortcode Directives**.
4. Configure the options by checking post types to support the shortcode directives.
5. Submit comments with the shortcode directives to perform certain operations against the selected posts in front-end. 


## Usage

<h4>$post_status</h4>
Sets a specified post status.
<strong>Format</strong>
```
[$post_status {status}]
```
<strong>Examples</strong>
Changes the post status to `pending`.
```
[$post_status pending]
```
Changes the post status to `draft`.
```
[$post_status draft]
```

<strong>Option: --to</strong>
Specifies which post to apply the directive. This `--to` option is avialble for all the directives except `$comment`. Use `children`, `siblings`, and `descendants` for builk actions.
- self (default): the comment/post iteself. When a comment with a directive is submitted to a post, that post will be the subject post and it is considered the one denoted by the option value, `self`.
- parent: the parent post of the subject post.
- {post ID}: the post ID.
- children: the direct child posts of the subjec post if the post type supports the `hierarchical` option.
- siblings: the sibling posts of the subject post in hierarchical relationships if the post type supports the `hierarchical` option.
- decendants: all the decendants which belong to the subject post if the post type supports the `hierarchical` option.

This moves all the descendant posts to trash.
```
[$post_status trash --to=descendants]
```

<h4>$post_parent</h4>
Sets a post parent ID.
<strong>Format</strong>
```
[$post_parent {post ID}]
```
Sets a post parent of a post ID of 1451.
```
[$post_parent 1451]
```
This removes a post parent.
```
[$post_parent 0]
```

<strong>Option: --to</strong>
The same option values are supported described in the `$post_status` section above.

<h4>$post_column</h4>
Sets a value with a specified post column name to an existent post column.
<strong>Format</strong>
```
[$post_column --column={colum name} {value}]
[$post_column --column={colum name} --value="some value"]
```

Setting the value `3` to the `menu_order` column.
```
[$post_column column=menu_order 3]
```

When a value must have a white space, use the `--value` option.
```
[$post_column --column=post_title --value="This is a title"]
```

<strong>Option: --to</strong>
The same option values are supported described in the `$post_status` section above.

<h4>$post_meta</h4>
Sets a post meta value.
<strong>Format</strong>
```
[$post_meta {key} {value}]
[$post_meta {key1}="{some value1}" {key2}="{some value2}" {key3}="{some value3}"...]
```

This sets the post meta value `bar` to the `_foo` meta key.
```
[$post_meta _foo bar]
```

For values containing white-spaces and multiple key-value pairs, use the attribute style format.
```
[$post_meta _question="Why did the chicken cross the road?" _answer="To get to the other side"]
```

To delete meta keys, use the `--action` option by passing `delete`. This deletes the `_question` and `_answer` meta keys and their values from the database.
```
[$post_meta _question _answer --action="delete"]
```

Note: the `--to` and `--action` option names are reserved by the plugin so you cannot specify them with the option name like `[$post_meta --action="my value" --to="another value"]`.

If you have to set them, use the command-line style format introduced above.
```
[$post_meta --action "Some value here."]
[$post_meta --to "Another value here."]
```

<strong>Option: --to</strong>
The same option values are supported described in the `$post_status` section above.

<h4>$taxonomy</h4>
Sets taxonomy terms with a specified taxonomy slug.
<strong>Format</strong>
```
[$taxonomy --slug={taxonomy slug} {term1} {term2} {term3}...]
```

This adds the `Apple`, `Banana`, `Apple Pie` terms of the `post_tag` taxonomy to the post.
```
[$taxonomy --slug=post_tag Apple Banana "Apple Pie"]
```

<strong>Option: --action</strong>
- add (default) : adds the specified terms
- remove : removes the specified terms
- remove_all/remove_all : removes all the associated terms
- delete : deletes the specified terms from the database if they are assigned to the post
- delete_all/delete-all : deletes all the associated terms from the database

This removes the `Apple`, `Banana`, `Apple Pie` terms of the `post_tag` taxonomy from the post.
```
[$taxonomy --slug=post_tag Apple Banana "Apple Pie" --action=remove]
```
This deletes the `Apple`, `Banana`, `Apple Pie` terms of the `post_tag` taxonomy from the database if they are assigned to the post.
```
[$taxonomy --slug=post_tag Apple Banana "Apple Pie" --action=delete]
```
This removes all the assigned terms of the `post_tag` taxonomy from the post.
```
[$taxonomy --slug=post_tag --action=remove_all]
```
This deletes all the assigned terms of the `post_tag` taxonomy from the database.
```
[$taxonomy --slug=post_tag --action=delete_all]
```

<strong>Option: --to</strong>
The same option values are supported described in the `$post_status` section above.

<h4>$tag</h4>
Sets non-hierarchical taxonomy terms.
<strong>Format</strong>
```
[$tag {tag1} {tag2} {tag3}...]
```

When the `$tag` directive is given, the plugin searches for a non-hierarchical taxonomy associated with the post type of the post and sets the given terms to the post. If a non-hierarchical taxonomy is not found, no action will be taken. If you have multiple non-hierarchical taxonomies for a particular post type which supports shortcode directives, use the `$taxonomy` directive. See the `$taxonomy` section above.

This adds the `Apple`, `Banana`, `Apple Pie` tags to the post.
```
[$tag Apple Banana "Apple Pie"]
```

<strong>Option: --action</strong>
The same action values with the `$taxonomy` directive are supported.

This removes the `Apple`, `Banana`, `Apple Pie` tags to the post.
```
[$tag --action=remove Apple Banana "Apple Pie"]
```
This deletes the `Apple`, `Banana`, `Apple Pie` terms from the database if they are assigned to the post.
```
[$tag --action=delete Apple Banana "Apple Pie"]
```
This removes all the tags associated with the post.
```
[$tag --action=remove_all]
```
This deletes all the tags associated with the post from the database.
```
[$tag --action=delete_all]
```

<strong>Option: --to</strong>
The same option values are supported described in the `$post_status` section above.

<h4>$category</h4>
Sets hierarchical taxonomy terms.
<strong>Format</strong>
```
[$category {category1} {category2} {category3}...]
```
When the `$category` directive is given, the plugin searches for a hierarchical taxonomy associated with the post type of the post and sets the given terms to the post. If a hierarchical taxonomy is not found, no action will be taken. If you have multiple hierarchical taxonomies for a particular post type which supports shortcode directives, use the `$taxonomy` directive. See the `$taxonomy` section above.

This adds the `Apple`, `Banana`, `Apple Pie` categories to the post.
```
[$category Apple Banana "Apple Pie"]
```
This removes the `Apple`, `Banana`, `Apple Pie` categories from the post.
```
[$category Apple Banana "Apple Pie" --action=remove]
```
This deletes the `Apple`, `Banana`, `Apple Pie` categories from the database if they are assigned to the post.
```
[$category --action=delete Apple Banana "Apple Pie"]
```
This removes all the categories associated with the post.
```
[$category --action=remove_all]
```
This deletes all the categories associated with the post from the database.
```
[$category --action=delete_all]
```

<strong>Option: --action</strong>
The same action values with the `$taxonomy` directive are supported.

<strong>Option: --to</strong>
The same option values are supported described in the `$post_status` section above.

<h5>$comment</h5>
Performs certain actions againt a replying comment. If commented on a post, the action applied to _all_ the comments which belong to the post. So be careful not to do that when you want to do someting on a single comment.

<strong>Format</strong>
```
[$comment {sub-command}]
```

<strong>Sub-commands</strong>
- delete|remove: deletes the comment
- hold|disapprove: changes the comment status to `hold`.
- trash: moves the comment to trash
- spam: mark the commet as spam
- convert: converts the comment to a child post.

Replying to an existent comment with the following deletes the comment.
```
[$comment delete]
```

For hierarchical post types, the `convert` sub-command can help bulk-convert comments in to posts.

Commenting on a _post_ (not an existent comment) converts all the comments belonging to the post into posts by keeping the hirarchical relationships.
```
[$comment convert]
```


## Bugs ##
If you find an issue, let us know [here](https://github.com/michaeluno/shortcode-directives/issues)!

## Support ##
This is a developer's portal for Shortcode Directives and should _not_ be used for support. Please visit the [support forums](http://wordpress.org/support/plugin/shortcode-directives).

## Contributions ##
Anyone is welcome to contribute to Shortcode Directives.

There are various ways you can contribute:

1. Raise an [Issue](https://github.com/michaeluno/shortcode-directives/issues) on GitHub.
2. Send us a Pull Request with your bug fixes and/or new features.
3. Provide feedback and suggestions on [enhancements](https://github.com/michaeluno/shortcode-directives/issues?direction=desc&labels=Enhancement&page=1&sort=created&state=open).

## Supporting Future Development ##

If you like it, please rate and review it in the [WordPress Plugin Directory](http://wordpress.org/support/view/plugin-reviews/shortcode-directives?filter=5). Also donation would be greatly appreciated. Thank you!

[![Donate with PayPal](https://www.paypal.com/en_US/i/btn/x-click-but04.gif)](http://en.michaeluno.jpdonate) 

## Copyright and License ##

### Shortcode Directives ###
Released under the [GPL v2](./LICENSE.txt) or later.
Copyright © 2015 Michael Uno

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.