document.addEventListener("DOMContentLoaded", function() {
  // 页面加载完成后获取帖子列表
  getPosts();
});

function getPosts() {
  fetch('forum.php')
    .then(response => response.json())
    .then(data => {
      if (data.posts) {
        // 渲染帖子列表
        renderPosts(data.posts);
      }
    })
    .catch(error => {
      console.error('获取帖子失败：', error);
    });
}


function renderPosts(posts) {
  const postsList = document.getElementById('posts-list');
  postsList.innerHTML = '';

  posts.forEach(post => {
    const postElement = document.createElement('div');
    postElement.classList.add('post');
    postElement.id = `post-${post.id}`;
    postElement.innerHTML = `
      <h2>${post.postTitle}</h2>
      <p>${post.postContent}</p>
      <div class="buttons">
        <button onclick="likePost(${post.id}, this)">👍 赞 (${post.likes})</button>
        <button onclick="dislikePost(${post.id}, this)">👎 不喜欢 (${post.dislikes})</button>
        <button onclick="showComments(${post.id})">💬 评论 (${post.comments.length})</button>
        <button onclick="sharePost(${post.id})">🔄 分享 (${post.shares})</button>
      </div>
      <div class="comments" style="display: none;">
        <h3>评论</h3>
        <ul id="comment-list-${post.id}"></ul>
        <input type="text" id="commentInput-${post.id}" placeholder="添加评论...">
        <button onclick="addComment(${post.id})">添加评论</button>
      </div>
    `;

    // 渲染评论列表
    renderComments(postElement, post.id, post.comments);

    // 动态设置评论按钮上的数量
    const commentsButton = postElement.querySelector(`#post-${post.id} button:nth-child(3)`);
    if (commentsButton) {
      commentsButton.innerHTML = `💬 评论 (${post.comments.length})`;
    } else {
      console.error(`未找到评论按钮的元素，postId: ${post.id}`);
    }

    postsList.appendChild(postElement);
  });
}


function renderComments(postElement, postId, comments) {
  const commentList = postElement.querySelector(`#comment-list-${postId}`);
  if (commentList) {
    commentList.innerHTML = '';

    comments.forEach(comment => {
      const commentElement = document.createElement('li');
      commentElement.textContent = comment.text;
      commentList.appendChild(commentElement);
    });
  } else {
    console.error(`未找到评论部分的元素，postId: ${postId}`);
  }
}



function submitPostForm() {
  const postTitle = document.getElementById('postTitle').value;
  const postContent = document.getElementById('postContent').value;

  if (postTitle.trim() !== '' && postContent.trim() !== '') {
    const postData = new URLSearchParams();
    postData.append('postTitle', postTitle);
    postData.append('postContent', postContent);

    fetch('forum.php', {
      method: 'POST',
      body: postData
    })
    .then(response => response.json())
    .then(data => {
      console.log(data); // 输出响应内容到控制台
      if (data.success) {
        // 发布成功后刷新帖子列表
        getPosts();
        // 清空发帖表单
        document.getElementById('new-post-form').reset();
      } else {
        console.error('帖子存储失败，错误：', data.error);
      }
    })
    .catch(error => {
      console.error('请求失败：', error);
    });
  }
}





function likePost(postId, button) {
  // 构建 URL 编码的数据字符串
  const data = new URLSearchParams();
  data.append('postId', postId);

  // 发送请求到服务器更新数据库
  fetch('like_post.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: data,
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // 更新按钮文本和点赞数
      button.innerHTML = `👍 赞 (${data.likes})`;
      // 可以在这里添加其他更新界面的逻辑
    } else {
      console.error('点赞失败，错误：', data.error);
      // 在控制台输出错误信息，或者进行其他错误处理
    }
  })
  .catch(error => {
    console.error('请求失败：', error);
    // 在控制台输出请求失败的信息，或者进行其他错误处理
  });
}



function dislikePost(postId, button) {
  // 构建 URL 编码的数据字符串
  const data = new URLSearchParams();
  data.append('postId', postId);

  // 发送请求到服务器更新数据库
  fetch('dislike_post.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: data,
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // 更新按钮文本和不喜欢数
      button.innerHTML = `👎 不喜欢 (${data.dislikes})`;
      // 可以在这里添加其他更新界面的逻辑
    } else {
      console.error('不喜欢失败，错误：', data.error);
    }
  })
  .catch(error => {
    console.error('请求失败：', error);
  });
}

function showComments(postId) {
  const commentsSection = document.querySelector(`#post-${postId} .comments`);
  if (commentsSection) {
    commentsSection.style.display = 'block';
  } else {
    console.error(`未找到评论部分的元素，postId: ${postId}`);
  }
  
}



function sharePost(postId) {
  // 处理分享逻辑，可以发送请求到服务器更新数据库
  alert(`帖子 ${postId} 分享！`);
}

function addComment(postId) {
  const commentInput = document.getElementById(`commentInput-${postId}`);
  const commentText = commentInput.value;

  if (commentText.trim() !== '') {
    const formData = new FormData();
    formData.append('postId', postId);
    formData.append('commentText', commentText);

    fetch('add_comment.php', {  // 确保这里的路径是正确的
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // 添加评论成功后刷新评论列表
        getPosts();
        // 清空评论输入框
        commentInput.value = '';
      } else {
        console.error('评论存储失败，错误：', data.error);
      }
    })
    .catch(error => {
      console.error('请求失败：', error);
    });
  }
}

document.addEventListener('DOMContentLoaded', function() {
  const myButton = document.getElementById('myButton');

  // 添加点击事件监听器
  myButton.addEventListener('click', function() {
    // 在这里可以添加其他动态效果，如动画或其他交互行为
    alert('确定要生成可视化报告吗');

    // 页面跳转
    window.location.href = myButton.href;
  });
});
