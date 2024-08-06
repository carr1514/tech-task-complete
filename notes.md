I have cleaned up the LookupController by defining each third-party service as a separate class and
implementing a common interface for them. This way, the controller is more readable and maintainable.

I have also added a caching mechanism to avoid calling the third-party services every time.
I have also added some unit tests to test the functionality of the steam service, this checks that the service returns
correctly if valid ID is provided, it also runs a test to check if the service returns an error if a username is provided

All services are dependency injected using AppServiceProvider, keeps the controller clean and allows for easier testing.


### Additional Notes ###

- I've tired to keep commenting light within the code, I feel like too much commenting can make the code harder to read.
- I've only added tests for the steam service, obviously would try to test all services in a real-world scenario.
- I've implemented a very simple caching system within the services, this could definitely be improved in a real-world scenario.
- I've added some validation into the LookupController however it's unclear how the third party apis might handle invalid data thrown at it. IE a null username or an id being a string rather than an integer, with a clearer scope a better validation system could be implemented for each service.
- There's parts of the code that might seem repetitive in particular the http requests to the third-party api's, i've left it like this just for the sake of the test, in a real-world scenario I would refactor this so that the http request was contained in its own method as all we do is change the url when we make the request, the response we get back is always the same from each service (id, username, avatar).
