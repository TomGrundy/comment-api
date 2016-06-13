# Lumen Comment API Example

To run:
- Copy `.env.example` to `.env`
- Modify the `.env` file to connect to a local database and give it an `APP_KEY`
- Create the database you named in the `.env` file
- Run migrations with `php artisan migrate`
- Seed the database with `php artisan db:seed`

Two users will be seeded into the database, one moderator and one standard user.
For requests from the standard user, set an `Api-Token` header of `abc123`.
For requests from the moderator, set an `Api-Token` header of `xyz789`.

## Endpoints

### api/v1/thread/{id}
Either creates a new thread or loads a thread and its comments, depending on if a `thread_id` parameter was provided or not.

### api/v1/comment
Creates or updates a comment.

###### Example post body
```
{
    body: 'some arbitrary bunch of text',
    thread_id: 1, // required
    parent_id: 2, // optional
    comment_id: 3 // optional, set if updating an existing comment
}
```

### api/v1/comment/{commentId}/moderate
Moderates a comment.

### api/v1/comment/{commentId}/rate/{ratingValue}
Adds or updates a rating of a comment.

###### Example post body
```
{
    rating_id: 4 // optional, set if updating an existing rating
}
```

### api/v1/comments/{userId}
Lists all comments for the given user ID.