<?php

use RMoore\ChangeRecorder\Change;

class ChangeRecorderTest extends TestCase
{
    /** @test */
    public function it_can_create_a_change()
    {
        $this->createChange();

        $this->assertEquals(Change::count(), 1);
    }

    /** @test */
    public function it_has_a_subject()
    {
        $post = $this->createPost()->fresh();
        $change = $this->createChange(['subject_id' => 1, 'subject_type' => Post::class]);

        $this->assertEquals($change->subject, $post);
    }

    /** @test */
    public function a_model_has_changes()
    {
        $post = $this->createPost();

        $this->assertCount(1, $post->fresh()->changes);

        $post->title = 'new title';
        $post->save();

        $this->assertCount(2, $post->fresh()->changes);
    }

    /** @test */
    public function it_names_created_events_correctly()
    {
        $post = $this->createPost();
        $this->assertEquals($post->changes->last()->event_name, 'created_post');
    }

    /** @test */
    public function it_names_updated_events_correcty_when_a_single_field_is_changed()
    {
        $post = $this->createPost();

        $post->title = 'new title';
        $post->save();

        $this->assertEquals($post->changes->last()->event_name, 'updated_post_title');
    }

    /** @test */
    public function it_names_updated_events_correcty_when_multiple_fields_are_changed()
    {
        $post = $this->createPost();

        $post->title = 'new title';
        $post->content = 'new content';

        $post->save();

        $this->assertEquals($post->changes->last()->event_name, 'updated_post');
    }

    /** @test */
    public function it_can_get_all_history()
    {
        $post = $this->createPost();
        $post->title = 'new title';
        $post->content = 'new content';
        $post->save();

        $history = $post->getHistory();
        $this->assertCount(2, $history);
    }

    /** @test */
    public function it_can_get_history_with_parameter()
    {
        $post = $this->createPost();
        $post->title = 'new title';
        $post->save();

        $history = $post->getHistory('title');
        $this->assertCount(1, $history);
    }

    /** @test */
    public function it_can_get_history_with_magic_method()
    {
        $post = $this->createPost();
        $post->title = 'new title';
        $post->save();

        $history = $post->titleHistory();
        $this->assertCount(1, $history);
    }

    /** @test */
    public function it_can_get_history_with_get_magic_method()
    {
        $post = $this->createPost();
        $post->title = 'new title';
        $post->save();

        $history = $post->getTitleHistory();
        $this->assertCount(1, $history);
    }
}
