<?php

namespace Tests\Feature;

use App\Contact;
use App\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactsTest extends TestCase
{
    use RefreshDatabase;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function a_list_of_contacts_can_be_fetched_for_the_authenticated_user()
    {
        $user = factory(User::class)->create();
        $anotherUser = factory(User::class)->create();

        $contact = factory(Contact::class)->create(['user_id' => $user->id]);
        $anotherContact = factory(Contact::class)->create(['user_id' => $anotherUser->id]);

        $response = $this->get('/api/contacts?api_token=' . $user->api_token);
        //dd(json_decode($response->getContent()));
        $response->assertJsonCount(1)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'contact_id' => $contact->id,
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function an_unauthenticated_user_should_redirect_to_login()
    {
        $response = $this->post('/api/contacts', array_merge($this->data(), ['api_token' => '']));

        $response->assertRedirect('/login');
        $this->assertCount(0, Contact::all());
    }

    /** @test */
    public function an_authenticated_user_can_add_a_contact()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/api/contacts', $this->data());

        $contact = Contact::first();

        //dd(json_decode($response->getContent()));

        $this->assertEquals('Test Name', $contact->name);
        $this->assertEquals('test@email.com', $contact->email);
        $this->assertEquals('05/14/1988', $contact->birthday->format('m/d/Y'));
        $this->assertEquals('ABC String', $contact->company);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'contact_id' => $contact->id,
            ],
            'links' => [
                'self' => $contact->path(),
            ]
        ]);
    }

    /** @test */
    public function fields_are_required()
    {
        collect(['name', 'email', 'birthday', 'company'])
        ->each(function ($field) {
            $response = $this->post('/api/contacts',
                array_merge($this->data(), [$field => '']));

            $response->assertSessionHasErrors($field);
            $this->assertCount(0, Contact::all());
        });
    }

    /** @test */
    public function email_must_be_a_valid_email()
    {
        $response = $this->post('/api/contacts',
            array_merge($this->data(), ['email' => 'NOT A VALID EMAIL']));

        $response->assertSessionHasErrors('email');
        $this->assertCount(0, Contact::all());
    }

    /** @test */
    public function birthdays_are_properly_stored()
    {
        $response = $this->post('/api/contacts',
            array_merge($this->data(), ['birthday' => '14 May, 1988']));

        $this->assertInstanceOf(Carbon::class, Contact::first()->birthday);
        $this->assertCount(1, Contact::all());
        $this->assertEquals('05-14-1988', Contact::first()->birthday->format('m-d-Y'));
    }

    /** @test */
    public function a_contact_can_be_retrieved()
    {
            $contact = factory(Contact::class)->create(['user_id' => $this->user->id]);

            $response = $this->get('/api/contacts/' . $contact->id .
                '?api_token=' . $this->user->api_token);
            //dd(json_decode($response->getContent()));
            $response->assertJson([
                'data' => [
                    'contact_id' => $contact->id,
                    'name' => $contact->name,
                    'email' => $contact->email,
                    'birthday' => $contact->birthday->format('m/d/Y'),
                    'company' => $contact->company,
                    'last_updated' => $contact->updated_at->diffForHumans(),
                ]
            ]);
    }

    /** @test */
    public function only_the_users_contacts_can_be_retrieved()
    {
        $contact = factory(Contact::class)->create(['user_id' => $this->user->id]);

        $anotherUser = factory(User::class)->create();

        $response = $this->get('/api/contacts/' . $contact->id .
            '?api_token=' . $anotherUser->api_token);

        $response->assertStatus(403);
    }

    /** @test */
    public function a_contact_can_be_patched()
    {
        $this->withoutExceptionHandling();
        $contact = factory(Contact::class)->create(['user_id' => $this->user->id]);

        $response = $this->patch('/api/contacts/' . $contact->id, $this->data());

        $contact = $contact->fresh();

        $this->assertEquals('Test Name', $contact->name);
        $this->assertEquals('test@email.com', $contact->email);
        $this->assertEquals('05/14/1988', $contact->birthday->format('m/d/Y'));
        $this->assertEquals('ABC String', $contact->company);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'contact_id' => $contact->id,
            ],
            'links' => [
                'self' => $contact->path(),
            ]
        ]);
    }

    /** @test */
    public function only_the_owner_can_patch_the_contact()
    {
        $contact = factory(Contact::class)->create();
        $anotherUser = factory(User::class)->create();

        $response = $this->patch('/api/contacts/' . $contact->id, array_merge($this->data(), ['api_token' => $anotherUser->api_token]));

        $response->assertStatus(403);

    }

    /** @test */
    public function a_contact_can_be_deleted()
    {
        $this->withoutExceptionHandling();
        $contact = factory(Contact::class)->create(['user_id' => $this->user->id]);

        $response = $this->delete('/api/contacts/' . $contact->id,
            ['api_token' => $this->user->api_token]);

        $this->assertCount(0, Contact::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function only_the_owner_can_delete_the_contact()
    {
        $contact = factory(Contact::class)->create();
        $anotherUser = factory(User::class)->create();

        $response = $this->delete('/api/contacts/' . $contact->id,
            ['api_token' => $this->user->api_token]);

        $response->assertStatus(403);

    }

    public function data()
    {
        return [
            'name' => 'Test Name',
            'email' => 'test@email.com',
            'birthday' => '05/14/1988',
            'company' => 'ABC String',
            'api_token' => $this->user->api_token,
        ];
    }
}
