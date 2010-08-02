			<div class="post" id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<small class="blogtitle"><?php echo get_option('blogname'); ?></small>
				<div class="postinfo">
					<div class="byline">
						<span class="avatar"><?php theme_author_avatar(); ?></span>
						<span class="author">Written by <?php theme_post_author(); ?></span>
					</div>
					<div class="date"><span class="verb">Published</span> on <?php the_time('F jS, Y'); ?></div>
					<div class="comments"><a href="<?php the_permalink() ?>#comments"><?php comments_number(''); ?></a></div>
					<?php $cats = get_the_category(); if ($cats[0]->name != 'Uncategorized'): ?>
					<div class="cats"><span class="verb">Posted</span> in <?php the_category(', '); ?></div>
					<?php endif; ?>
				</div>

				<div class="entry">
					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>

				<div class="postmetadata">
					<?php the_tags( '<div>Tags: ', ', ', '</div>'); ?>
				</div>

			</div>
