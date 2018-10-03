@extends('layouts.app')

@section('content')
  <div class="container">
      <h1>number of users: @{{ count }}</h1>
    <h1>{{ $post->title }}</h1>
    {{ $post->updated_at->toFormattedDateString() }}
    @if ($post->published)
      <span class="label label-success" style="margin-left:15px;">Published</span>
    @else
      <span class="label label-default" style="margin-left:15px;">Draft</span>
    @endif
    <hr />
    <p class="lead">
      {{ $post->content }}
    </p>
    <hr />

    <h3>Comments:</h3>
    <div style="margin-bottom:50px;" v-if="user">
      <textarea class="form-control" rows="3" name="body" placeholder="Leave a comment" v-model="commentBox"></textarea>
      <button class="btn btn-success" style="margin-top:10px" @click.prevent="postComment">Save Comment</button>
    </div>
    <div v-else>
        <h4>Log in to leave comment</h4>
    </div>


    <div class="media" style="margin-top:20px;" v-for="comment in comments">
      <div class="media-left">
        <a href="#">
          <img class="media-object" src="http://placeimg.com/80/80" alt="...">
        </a>
      </div>
      <div class="media-body">
        <h4 class="media-heading">@{{ comment.user.name }} said...</h4>
        <p>
            @{{ comment.body }}
        </p>
        <span style="color: #aaa;">on @{{ comment.created_at }}</span>
      </div>
    </div>
  </div>
@endsection


@section('scripts')
  <script>
      const app = new Vue({
          el: '#app',
          data: {
              comments: {},
              commentBox: '',
              post: {!! $post->toJson() !!},
              user: {!! \Auth::check() ? \Auth::user()->toJson() : json_encode(['user' => ['id' => 76234, 'name' => 'guest']]) !!},
              count: 0,
              channelName: '{!! $channel_name !!}',
              users: []
          },
          mounted() {
              this.getComments();
              this.listen();
          },
          methods: {
              getComments() {
                  axios.get(`/api/posts/${this.post.id}/comments`)
                      .then((response) => {
                          this.comments = response.data;
                      })
                      .catch(function (error) {
                          console.log(error)
                      })

              },
              postComment() {
                  axios.post(`/api/posts/${this.post.id}/comment`, {
                          api_token: this.user.api_token,
                          body: this.commentBox
                      })
                      .then((response) => {
                          this.comments.unshift(response.data);
                          this.commentBox = '';
                      })
                      .catch(function (error) {
                          console.log(error);
                      })
              },
              listen() {
                  console.log(this.channelName);

                  Echo.channel(`posts.${this.post.id}.count`)
                      .listen('PostViewersCount', (e) => {
                          console.log(e.count);
                          this.count = e.count;
                      });
                  Echo.channel(this.channelName)
                      .listen('RandomChannel', (e) => {
                          console.log('slusam');
                      });
                  //Pusher.allChannels().forEach(channel => console.log(channel));
              },
          },
      });
  </script>
@endsection