# SIMPLE RESTFULL PHP API


! THIS PROJECT IS NOT YET FINISHED !

Current issues: 

- lack of refactoring
- controllers missing
- more error cases and exeptions to handle

This one was an interview test that I failed.
I decided to do it again but on my own and from scratch for the sake of getting back to the php devlopment and because I think it's a cool use case.

## SPECS
- No framework
- PDO
- RESTFUL URL (.htaccess)

## THE API
`_GET /books`
> get all books from the library database

`_GET /books/{id}`
> get specified book (by id)

`_POST /books` 
> post a new book and show the record just after the query
- optional: order = ('title' or 'author')

`_GET /author/{name}/books`
> get all the books from a given author name
- mandatory: {name}
- optional: order = ('id' or 'title')