-------------------------Current Database Structure---------------------
CREATE TABLE users (
  id SERIAL NOT NULL,
  name VARCHAR(20) NOT NULL,
  email VARCHAR(30) NOT NULL,
  email_verified_at TIMESTAMP(0),
  password VARCHAR(255),
  remember_token VARCHAR(100),
  created_at TIMESTAMP(0),
  updated_at TIMESTAMP(0),
  password1 VARCHAR(255),
  password2 VARCHAR(255),
  password3 VARCHAR(255),
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
  public_id VARCHAR(500),
  google_id VARCHAR(500),
  PRIMARY KEY (id),
  UNIQUE (email)
);

CREATE TABLE user_avatars
(
  id SERIAL NOT NULL,
  user__id int4 NOT NULL,
  seed int4,
  default_avatar_url VARCHAR(500) NOT NULL,
  is_using_default BOOLEAN NOT NULL DEFAULT TRUE,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
  img_url VARCHAR(500) NULL,
  created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
  PRIMARY KEY (id),
  FOREIGN KEY (user__id) REFERENCES users(id),
  UNIQUE (seed)
);

CREATE TABLE countries
(
    id SERIAL NOT NULL,
    code VARCHAR(10) NOT NULL,
    name_en VARCHAR(500) NOT NULL,
    name_kh VARCHAR(500) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    UNIQUE (code)
);
INSERT INTO countries(code, name_en, name_kh) VALUES ('kh','Cambodia','កម្ពុជា');
INSERT INTO countries(code, name_en, name_kh) VALUES ('th','Thailand','ថៃ');
INSERT INTO countries(code, name_en, name_kh) VALUES ('vn','Vietnam','វៀតណាម');
INSERT INTO countries(code, name_en, name_kh) VALUES ('ph','Philippines','ភីលីពីន');
INSERT INTO countries(code, name_en, name_kh) VALUES ('la','Laos','ឡាវ');
INSERT INTO countries(code, name_en, name_kh) VALUES ('sg','Singapore','សិង្ហបុរី');
INSERT INTO countries(code, name_en, name_kh) VALUES ('my','Malaysia','ម៉ាឡេស៊ី');
INSERT INTO countries(code, name_en, name_kh) VALUES ('bn','Brunei','ព្រុយនេ');
INSERT INTO countries(code, name_en, name_kh) VALUES ('id','Indonesia','ឥណ្ឌូនេស៊ី');
INSERT INTO countries(code, name_en, name_kh) VALUES ('mm','Burma','មីយ៉ាន់ម៉ា');

CREATE TABLE user_profiles
(
    id SERIAL NOT NULL,
    user__id int4 NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
    full_name VARCHAR(500) NULL,
    country__id int4 NULL,
    location VARCHAR(500) NULL,
    position VARCHAR(500) NULL,
    about_me VARCHAR(1000) NULL,
    website_link VARCHAR(500) NULL,
    facebook_link VARCHAR(500) NULL,
    twitter_link VARCHAR(500) NULL,
    telegram_link VARCHAR(500) NULL,
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    FOREIGN KEY (user__id) REFERENCES users(id),
    FOREIGN KEY (country__id) REFERENCES countries(id)
);

CREATE TABLE subjects
(
    id SERIAL NOT NULL,
    public_id VARCHAR(500) NOT NULL,
    name_en VARCHAR(500) NOT NULL,
    name_kh VARCHAR(500) NOT NULL,
    description_en VARCHAR(1000) NOT NULL,
    description_kh VARCHAR(1000) NOT NULL,
    img_url VARCHAR(500) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    UNIQUE (public_id)
);
INSERT INTO subjects(public_id,name_en,name_kh,description_en,description_kh,img_url) VALUES('default','Default','កខគ','Default','កខគ','\icons\subjects\subject.png');
INSERT INTO subjects(public_id,name_en,name_kh,description_en,description_kh,img_url) VALUES('programming','Programming','កខគ','A creative process that instructs a computer on how to do a task','កខគ','\icons\subjects\programming.png');
INSERT INTO subjects(public_id,name_en,name_kh,description_en,description_kh,img_url) VALUES('khmer_literature','Khmer Literature','កខគ','The study of Khmer language','កខគ','\icons\subjects\english.png');
INSERT INTO subjects(public_id,name_en,name_kh,description_en,description_kh,img_url) VALUES('english_literature','English Literature','កខគ','The study of English language','កខគ','\icons\subjects\khmer.png');

CREATE TABLE questions
(
    id SERIAL NOT NULL,
    public_id VARCHAR(500) NOT NULL,
    title VARCHAR(1000) NOT NULL,
    user__id int4 NOT NULL,
    subject__id int4 NOT NULL,
    is_draft BOOLEAN NOT NULL DEFAULT FALSE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
    posted_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    FOREIGN KEY (user__id) REFERENCES users(id),
    FOREIGN KEY (subject__id) REFERENCES subjects(id),
    UNIQUE (public_id)
);

CREATE TABLE question_descriptions
(
    id SERIAL NOT NULL,
    question__id int4 NOT NULL,
    data TEXT NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    FOREIGN KEY (question__id) REFERENCES questions(id)
);

CREATE TABLE tags
(
    id SERIAL NOT NULL,
    subject__id int4 NOT NULL,
    public_id VARCHAR(500) NOT NULL,
    name_en VARCHAR(500) NOT NULL,
    name_kh VARCHAR(500) NOT NULL,
    description_en VARCHAR(1000) NOT NULL,
    description_kh VARCHAR(1000) NOT NULL,
    img_url VARCHAR(500) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    FOREIGN KEY (subject__id) REFERENCES subjects(id),
    UNIQUE (public_id)
);
INSERT INTO tags(subject__id,public_id,name_en,name_kh,description_en,description_kh,img_url) VALUES(2,'php','PHP','កខគ','Hypertext Preprocessor It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout','កខគ','\icons\tags\php.png');
INSERT INTO tags(subject__id,public_id,name_en,name_kh,description_en,description_kh,img_url) VALUES(2,'javascript','JavaScript','កខគ','JavaScript It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout','កខគ','\icons\tags\javascript.png');
INSERT INTO tags(subject__id,public_id,name_en,name_kh,description_en,description_kh,img_url) VALUES(3,'proverb','Proverb','កខគ','Proverb It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout','កខគ','\icons\tags\proverb.png');
INSERT INTO tags(subject__id,public_id,name_en,name_kh,description_en,description_kh,img_url) VALUES(4,'grammar','Grammar','កខគ','Grammar It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout','កខគ','\icons\tags\grammar.png');
INSERT INTO tags(subject__id,public_id,name_en,name_kh,description_en,description_kh,img_url) VALUES(4,'vocabulary','Vocabulary','កខគ','Vocabulary It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout','កខគ','\icons\tags\vocabulary.png');

CREATE TABLE question_tag_mappings
(
    question__id int4 NOT NULL,
    tag__id int4 NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (question__id,tag__id)
);

CREATE TABLE third_party_api_urls
(
  id SERIAL NOT NULL,
  key VARCHAR(500) NOT NULL,
  value VARCHAR(500) NOT NULL,
  is_active BOOLEAN NOT NULL DEFAULT true,
  description VARCHAR(500) NOT NULL,
  created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
  PRIMARY KEY (id)
);
INSERT INTO third_party_api_urls(key,value,description) VALUES('jdenticon','https://avatars.dicebear.com/v2/jdenticon/{{PLACEHOLDER}}.svg','Open source library for generating identicons (avatar profile). Have placeholder as seed(=random string)');

CREATE TABLE system_messages
(
    id SERIAL NOT NULL,
    code VARCHAR(100) NOT NULL,
    message_sys VARCHAR(500),
    message_en VARCHAR(500),
    message_kh VARCHAR(500),
    type VARCHAR(50) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT true,
    description VARCHAR(500),
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    UNIQUE (code)
);
---Success
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__USER_REGISTER','User is created successfully','Welcome','សូមស្វាគមន៍','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__USER_CHANGE_PASSWORD','Password is changed successfully','Password is changed successfully.','ពាក្យសម្ងាត់ត្រូវបានប្តូរថ្មីដោយជោគជ័យ','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__USER_LOGIN','User logs in successfully','Welcome again','សូមស្វាគមន៍ជាថ្មីម្ដងទៀត','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__USER_LOGOUT','User logs out successfully','See you next time','ជួបគ្នាលើកក្រោយទៀត','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__USER_IS_AUTHENTICATED','User is authenticated','User is authenticated','គណនីនេះត្រឹមត្រូវ','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__QUESTION_SAVE','Question is saved successfully','Your question is posted successfully','សំណួររបស់អ្នកបានបង្ហោះជាសាធារណៈដោយជោគជ័យ','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__QUESTION_SAVE_DRAFT','Question is saved as draft successfully','Your drafted question is saved successfully','ពង្រាងនៃសំណួររបស់អ្នកបានរក្សាទុកដោយជោគជ័យ','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__ANSWER_SAVE','Answer is saved successfully','Your answer is posted successfully','ចម្លើយរបស់អ្នកបានបង្ហោះជាសាធារណៈដោយជោគជ័យ','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__ANSWER_SAVE_DRAFT','Answer is saved as draft successfully','Your drafted answer is saved successfully','ពង្រាងនៃចម្លើយរបស់អ្នកបានរក្សាទុកដោយជោគជ័យ','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__COMMENT_SAVE','Comment is saved successfully','Your comment is posted successfully','មតិយោបល់របស់អ្នកបានបង្ហោះជាសាធារណៈដោយជោគជ័យ','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__REPLY_SAVE','Reply is saved successfully','Your reply to the comment is posted successfully','ការឆ្លើយតបរបស់អ្នកទៅនឹងមតិយោបល់បានបង្ហោះជាសាធារណៈដោយជោគជ័យ','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__ROLE_SAVE','Role is saved successfully','Role is saved successfully','តួនាទីបានបង្កើតដោយជោគជ័យ','info');
---Error
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__JWT_EXCEPTION','Token could not be parsed from the request','Please login again','សូមចូលភ្ជាប់គណនីម្ដងទៀត','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__TOKEN_NOT_PROVIDED','Token not provided','Please login again','សូមចូលភ្ជាប់គណនីម្ដងទៀត','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__UNKNOWN_CLIENT','Client credentials are not provided','Please login again','សូមចូលភ្ជាប់គណនីម្ដងទៀត','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__INVALID_TOKEN','Invalid Token','Please login again','សូមចូលភ្ជាប់គណនីម្ដងទៀត','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__EXPIRED_TOKEN','Expired Token','Session expires, please login again','គណនីអស់សុពលភាព សូមចូលភ្ជាប់គណនីម្ដងទៀត','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__UNAUTHENTICATED_USER','Unauthenticated user','Please login again','សូមចូលភ្ជាប់គណនីម្ដងទៀត','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__UNAUTHORIZED_ACCESS','Unauthorized Access','You do not have permission to access this function','អ្នកមិនមានសិទ្ធិចូលប្រើប្រាស់មុខងារនេះទេ','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__TOKEN_BLACKLISTED','Token blacklisted','You are not authenticated to access this','អ្នកមិនមានគណនីក្នុងប្រព័ន្ធកម្មវិធីនេះទេ','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__INTERNAL_SERVER_ERROR','Internal Server Error','Our server is sleeping, please try again in 5 minutes','ប្រព័ន្ធកម្មវិធីរបស់យើងកំពុងសម្រាក សូមព្យាយាមម្តងទៀតនៅ៥នាទីក្រោយ','error');

INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__EMAIL_OR_PASSWORD_INCORRECT','Email or password is incorrect','Your email or password is incorrect','អ៊ីមែល ឬពាក្យសម្ងាត់របស់អ្នកមិនត្រឹមត្រូវទេ','error');

INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__CURRENT_PASSWORD_NOT_CORRECT','The current password is not correct','Your current password is not correct','ពាក្យសម្ងាត់បច្ចុប្បន្នរបស់អ្នកមិនត្រឹមត្រូវទេ','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__NEW_PASSWORD_SAME_LAST_THREE','Your new password must not be the same as your last 3 passwords','Your new password must not be the same as your last 3 passwords','ពាក្យសម្ងាត់ថ្មីមិនអាចដូចនឹងពាក្យសម្ងាត់ចាស់ពីមុន៣របស់អ្នកទេ','error');

INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__QUESTION_NOT_EXIST','Question with this public id does not exist','Your searched question does not exist','មិនមានសំណួរដែលអ្នកកំពុងស្វែងរកទេ','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__SUBJECT_NOT_EXIST','Subject with this public id does not exist','Your searched subject does not exist','មិនមានមុខវិជ្ជាដែលអ្នកកំពុងស្វែងរកទេ','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__ANSWER_NOT_EXIST','Answer with this public id does not exist','Your searched answer does not exist','មិនមានចម្លើយដែលអ្នកកំពុងស្វែងរកទេ','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__COMMENTABLE_MODEL_NOT_EXIST','Commentable model with this public id does not exist','Your searched commentable model does not exist','មិនមានម៉ូដែលត្រូវបានផ្ដល់យោបល់ទេ','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__COMMENT_NOT_EXIST','Comment with this public id does not exist','Your searched comment does not exist','មិនមានមតិយោបល់ដែលអ្នកកំពុងស្វែងរកទេ','error');
---Validation
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__NAME_REQUIRED','Name is required','Please provide your name','សូមបញ្ចូលឈ្មោះរបស់អ្នក','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__NAME_STRING','Name must be a string','Please enter a valid name','សូមបញ្ចូលឈ្មោះឲ្យបានត្រឹមត្រូវ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__NAME_MAX_50','Name must not exceed 50 characters','Please shorten your name to less than 50 characters','អ្នកបានចុះឈ្មោះជាមួយអ៊ីមែលនេះរួចហើយ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__EMAIL_REQUIRED','Email is required','Please provide your email','សូមបញ្ចូលអ៊ីមែលរបស់អ្នក','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__EMAIL_EMAIL','The email is not in correct format','Please enter a valid email address','សូមបញ្ចូលអ៊ីមែលឲ្យបានត្រឹមត្រូវ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__EMAIL_UNIQUE_USERS_EMAIL','The email has already existed in the system','You have already registered with this email','អ្នកបានចុះឈ្មោះជាមួយអ៊ីមែលនេះរួចហើយ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__PASSWORD_REQUIRED','Password is required','Please provide a password','សូមបញ្ចូលពាក្យសម្ងាត់','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__PASSWORD_MIN_8','Password must have at least 8 characters','Password must have at least 8 characters','ពាក្យសម្ងាត់ត្រូវមានយ៉ាងតិច៨តួអក្សរ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__CURRENT_PASSWORD_REQUIRED','Current password is required','Please provide your current password','សូមបញ្ចូលពាក្យសម្ងាត់បច្ចុប្បន្នរបស់អ្នក','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__NEW_PASSWORD_REQUIRED','New password is required','Please provide your new password','សូមបញ្ចូលពាក្យសម្ងាត់ថ្មីរបស់អ្នក','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__NEW_PASSWORD_MIN_8','New password must have at least 8 characters','Your new password must have at least 8 characters','ពាក្យសម្ងាត់ថ្មីរបស់អ្នកត្រូវមានយ៉ាងតិច៨តួអក្សរ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__NEW_PASSWORD_DIFFERENT_CURRENT_PASSWORD','Current and new password must not be the same','Your new password must not be the same as current password','ពាក្យសម្ងាត់បច្ចុប្បន្ន និងថ្មីរបស់អ្នកមិនអាចដូចគ្នាបានទេ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__NEW_PASSWORD_CONFIRMED','Confirmed new password does not match','Confirmed new password does not match','ពាក្យសម្ងាត់ថ្មី និងការបញ្ជាក់ពាក្យសម្ងាត់ថ្មីមិនត្រូវគ្នាទេ','warning');

INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__TITLE_REQUIRED','Title is required','Please provide a title​ for your question','សូមបញ្ចូលចំណងជើងនៃសំណួររបស់អ្នក','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__TITLE_STRING','Title must be a string','Title is not a valid string','ចំណងជើងមានទម្រង់មិនត្រឹមត្រូវទេ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__TITLE_MAX_250','Title must not exceed 250 characters','Please shorten your title to less than 250 characters','សូមសម្រួលចំណងជើងនៃសំណួររបស់អ្នកឲ្យនៅតិចជាង២៥០តួអក្សរ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__DESCRIPTION_REQUIRED','Description is required','Please provide description for your question','សូមបញ្ចូលការពិព័រនានៃសំណួររបស់អ្នក','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__DESCRIPTION_STRING','Description must be a string','Description is not a valid string','ការពិព័រនាមានទម្រង់មិនត្រឹមត្រូវទេ','warning');

INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__PUBLIC_ID_REQUIRED','public_id is required','Please provide a public id','','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__PUBLIC_ID_STRING','public_id must be a string','Public id is not a valid string','','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__IS_DRAFT_REQUIRED','is_draft is required','Please specify if it is a draft','','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__IS_DRAFT_BOOLEAN','is_draft must be a boolean','Value to specify for a draft must be true or false','','warning');

INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__SUBJECT_PUBLIC_ID_REQUIRED','subject_public_id is required','Please provide public id of the subject','សូមបញ្ចូលកូដសម្គាល់របស់មុខវិជ្ជា','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__SUBJECT_PUBLIC_ID_STRING','subject_public_id must be a string','Given public id of the subject is not valid','កូដសម្គាល់របស់មុខវិជ្ជាមានទម្រង់មិនត្រឹមត្រូវទេ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__SUBJECT_PUBLIC_ID_MAX_500','subject_public_id must not exceed 500 characters','Public id of the subject must not exceed 500 characters','កូដសម្គាល់របស់មុខវិជ្ជាមិនត្រូវលើសពី ៥០០ តួអក្សរទេ','warning');

INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__QUESTION_PUBLIC_ID_REQUIRED','question_public_id is required','Please provide public id of the question','សូមបញ្ចូលកូដសម្គាល់របស់សំណួរ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__QUESTION_PUBLIC_ID_STRING','question_public_id must be a string','Given public id of the question is not valid','កូដសម្គាល់របស់សំណួរមានទម្រង់មិនត្រឹមត្រូវទេ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__QUESTION_PUBLIC_ID_MAX_500','question_public_id must not exceed 500 characters','Public id of the question must not exceed 500 characters','កូដសម្គាល់របស់សំណួរមិនត្រូវលើសពី ៥០០ តួអក្សរទេ','warning');

INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__COMMENTABLE_PUBLIC_ID_REQUIRED','commentable_public_id is required','Please provide public id of the commentable model','សូមបញ្ចូលកូដសម្គាល់របស់ម៉ូដែលត្រូវបានផ្ដល់យោបល់','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__COMMENTABLE_PUBLIC_ID_STRING','commentable_public_id must be a string','Give commentable public id is not valid','កូដសម្គាល់របស់ម៉ូដែលត្រូវបានផ្ដល់យោបល់មានទម្រង់មិនត្រឹមត្រូវទេ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__COMMENTABLE_TYPE_REQUIRED','commentable_type is required','Please provide type of the commentable model','សូមបញ្ចូលប្រភេទរបស់ម៉ូដែលត្រូវបានផ្ដល់យោបល់','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__COMMENTABLE_TYPE_STRING','commentable_type must be a string','Give commentable type is not valid','ប្រភេទរបស់ម៉ូដែលត្រូវបានផ្ដល់យោបល់មានទម្រង់មិនត្រឹមត្រូវទេ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__BODY_REQUIRED','body is required','Please provide body of the comment','សូមបញ្ចូលមតិយោបល់','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__BODY_STRING','body must be a string','Given body of the comment is not valid','មតិយោបល់មានទម្រង់មិនត្រឹមត្រូវទេ','warning');

INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__COMMENT_PUBLIC_ID_REQUIRED','comment_public_id is required','Please provide public id of the comment','សូមបញ្ចូលកូដសម្គាល់របស់មតិយោបល់','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__COMMENT_PUBLIC_ID_STRING','comment_public_id must be a string','Given public id of the comment is not valid','កូដសម្គាល់របស់មតិយោបល់មានទម្រង់មិនត្រឹមត្រូវទេ','warning');

INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__PICTURE_REQUIRED','picture is required','Please provide picture of Google profile','សូមបញ្ចូលតំណរូបភាពរបស់គណនីGoogle','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__GOOGLE_ID_REQUIRED','google_id is required','Please provide Google ID','សូមបញ្ចូលលេខសម្គាល់របស់គណនីGoogle','warning');

INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__NAME_UNIQUE_ROLES_NAME','The role name is already existed in the system','This role name is already existed in the system','ឈ្មោះតួនាទីនេះមាននៅក្នុងប្រព័ន្ធរួចហើយ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__ROLE_ID_REQUIRED','role_id is required','Please provide role ID','សូមបញ្ចូលលេខសម្គាល់របស់តួនាទី','warning');

CREATE TABLE answers
(
    id SERIAL NOT NULL,
    public_id VARCHAR(500) NOT NULL,
    user__id int4 NOT NULL,
    question__id int4 NOT NULL,
    is_draft BOOLEAN NOT NULL DEFAULT FALSE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
    is_accepted BOOLEAN NOT NULL DEFAULT FALSE,
    accepted_at TIMESTAMP(0) NULL,
    posted_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    FOREIGN KEY (user__id) REFERENCES users(id),
    FOREIGN KEY (question__id) REFERENCES questions(id),
    UNIQUE (public_id)
);

CREATE TABLE answer_descriptions
(
    id SERIAL NOT NULL,
    answer__id int4 NOT NULL,
    data TEXT NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    FOREIGN KEY (answer__id) REFERENCES answers(id)
);

CREATE TABLE comments
(
    id SERIAL NOT NULL,
    public_id VARCHAR(50) NOT NULL,
    body text NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
    user__id int4 NOT NULL,
    commentable_id int4 NOT NULL,
    commentable_type VARCHAR(50),
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    FOREIGN KEY (user__id) REFERENCES users(id),
    UNIQUE (public_id)
);

CREATE TABLE replies
(
    id SERIAL NOT NULL,
    public_id VARCHAR(50) NOT NULL,
    body text NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
    user__id int4 NOT NULL,
    comment__id int4 NOT NULL,
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    FOREIGN KEY (user__id) REFERENCES users(id),
    FOREIGN KEY (comment__id) REFERENCES comments(id),
    UNIQUE (public_id)
);

CREATE TABLE logs
(
    id SERIAL NOT NULL,
    log_level VARCHAR(10) NOT NULL,
    error_code VARCHAR(20),
    request_id VARCHAR(50),
    request_type VARCHAR(50),
    message TEXT,
    controller VARCHAR(100),
    method VARCHAR(100),
    loc int4,
    file VARCHAR(500),
    url TEXT,
    input_data TEXT,
    old_data TEXT,
    new_data TEXT,
    trace TEXT,
    is_resolved BOOLEAN NOT NULL DEFAULT FALSE,
    is_concerned BOOLEAN NOT NULL DEFAULT FALSE,
    created_by int4 NULL,
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
CREATE TABLE roles (
    id SERIAL NOT NULL,
    name VARCHAR(500) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
    created_by int4 NOT NULL,
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_by int4 NULL,
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    UNIQUE(name)
);
CREATE TABLE user_role_mappings (
    user__id int4 NOT NULL,
    role__id int4 NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
    created_by int4 NOT NULL,
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_by int4 NULL,
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (user__id, role__id),
    FOREIGN KEY (user__id) REFERENCES users(id),
    FOREIGN KEY (role__id) REFERENCES roles(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);
CREATE TABLE permissions (
    id SERIAL NOT NULL,
    name VARCHAR(500) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
    created_by int4 NOT NULL,
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_by int4 NULL,
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    UNIQUE(name)
);
CREATE TABLE role_permission_mappings (
    role__id int4 NOT NULL,
    permission__id int4 NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
    created_by int4 NOT NULL,
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_by int4 NULL,
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (role__id, permission__id),
    FOREIGN KEY (role__id) REFERENCES roles(id),
    FOREIGN KEY (permission__id) REFERENCES permissions(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);
