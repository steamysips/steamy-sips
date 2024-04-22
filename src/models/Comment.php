<?php

declare(strict_types=1);

namespace Steamy\Model;

use DateTime;
use Exception;
use Steamy\Core\Model;

class Comment
{
    use Model;

    protected string $table = 'comment';

    private int $user_id;
    private int $review_id;
    private int $comment_id;
    private ?int $parent_comment_id;
    private string $text;
    private Datetime $created_date;

    public function __construct(
        int $user_id = null,
        int $review_id = null,
        int $comment_id = null,
        ?int $parent_comment_id = null,
        string $text = '',
        ?DateTime $created_date = null
    ) {
        $this->user_id = $user_id ?? -1;
        $this->review_id = $review_id ?? -1;
        $this->comment_id = $comment_id ?? -1;
        $this->parent_comment_id = $parent_comment_id;
        $this->text = $text;
        $this->created_date = $created_date ?? new DateTime();
    }

    /**
     * Retrieves a review by its ID.
     *
     * @param int $comment_id The ID of the review to retrieve.
     * @return Comment|null The review object if found, otherwise null.
     */
    public static function getByID(int $comment_id): ?Comment
    {
        if ($comment_id < 0) {
            return null;
        }

        $query = "SELECT * FROM comment WHERE comment_id = :id";
        $params = ['id' => $comment_id];

        $result = Comment::query($query, $params); // Execute the query

        if (empty($result)) {
            return null;
        }

        return new Comment(
            user_id: $result[0]->user_id,
            review_id: $result[0]->review_id,
            comment_id: $result [0]->comment_id,
            text: $result[0]->text,
            created_date: $result[0]->created_date
        );
    }


    public function validate(): array
    {
        // TODO: Complete
        $errors = [];

        if (strlen($this->text) < 2) {
            $errors['text'] = "Review text must have at least 2 characters";
        }

        if ($this->created_date > new DateTime()) {
            $errors['date'] = "Review date cannot be in the future";
        }

        return $errors;
    }

    /**
     * Saves comment to database if attributes are valid. comment_id and created_date attributes
     * are automatically set by database and any set values are ignored.
     * @return bool
     */
    public function save(): bool
    {
        // if attributes are invalid, exit
        if (count($this->validate()) > 0) {
            return false;
        }

        // get data to be inserted into the review table
        $data = $this->toArray();

        // remove review_id as it is auto-incremented in database
        unset($data['comment_id']);

        // remove created_date (let database handle it)
        unset($data['created_date']);

        // perform insertion to the review table
        try {
            $this->insert($data, $this->table);
            return true;
        } catch (Exception) {
            return false;
        }
    }

    public function toArray(): array
    {
        return
            [
                'comment_id' => $this->comment_id,
                'user_id' => $this->user_id,
                'review_id' => $this->comment_id,
                'parent_comment_id' => $this->parent_comment_id,
                'text' => $this->text,
                'created_date' => $this->created_date->format('Y-m-d H:i:s'),
            ];
    }

    public function getUserID(): int
    {
        return $this->user_id;
    }

    public function getReviewID(): int
    {
        return $this->review_id;
    }

    public function getCommentID(): int
    {
        return $this->comment_id;
    }

    public function getParentCommentID(): ?int
    {
        return $this->parent_comment_id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getCreatedDate(): DateTime
    {
        return $this->created_date;
    }

    public function setUserID(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function setReviewID(int $review_id): void
    {
        $this->review_id = $review_id;
    }

    public function setCommentID(int $comment_id): void
    {
        $this->comment_id = $comment_id;
    }

    public function setParentCommentID(?int $parent_comment_id): void
    {
        $this->parent_comment_id = $parent_comment_id;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function setCreatedDate(DateTime $created_date): void
    {
        $this->created_date = $created_date;
    }
}