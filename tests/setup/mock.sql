INSERT INTO `uri`(`id`, `uri`) VALUES
    ('1', '/mock/api/testing'),
    ('2', '/uri/api/testing');

INSERT INTO `mock`(`id`, `uri_id`, `request_query`, `request_post`, `response_header`, `response_body`, `timeout`) VALUES
    ('1', '1', '{}', '{}', '{}', '{s:2}', '2000'), 
    ('2', '1', '{"key":"get"}', '{"key":"post"}', '{"Content-Type":"text\/html"}', '{s:3}', '4000'), 
    ('3', '2', '{"key":"get"}', '{"key":"post"}', '{"Content-Type":"text\/html"}', '{s:3}', '4000');