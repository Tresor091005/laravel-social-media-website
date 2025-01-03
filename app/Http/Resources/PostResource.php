<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $comments = $this->comments;

        return [
            'id' => $this->id,
            'body' => $this->body,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'user' => new UserResource($this->user),
            'group' => new GroupResource($this->group),
            'attachments' => PostAttachmentResource::collection($this->attachments),
            'num_of_reactions' => $this->reactions_count,
            'num_of_comments' => count($comments),
            'current_user_has_reaction' => $this->reactions->count() > 0,
            'comments' => self::convertCommentsIntoTree($comments)
        ];
    }

    /**
     *
     * @param \App\Models\Comment[] $comments
     * @param int|null $parentId
     * @return array
     */
    private static function convertCommentsIntoTree($comments, $parentId = null): array
    {
        //TODO optimize for larger application
        $commentTree = [];

        foreach ($comments as $comment) {
            // Find all comments which has the given parentId as $comment->parent_id
            if ($comment->parent_id === $parentId) {
                // Create the sub tree of CommentRessource
                $children = self::convertCommentsIntoTree($comments, $comment->id);
                $comment->childComments = $children;
                $comment->numOfComments = count($children) + collect($children)->sum('numOfComments');

                $commentTree[] = new CommentResource($comment);
            }
        }

        return $commentTree;
    }
}
