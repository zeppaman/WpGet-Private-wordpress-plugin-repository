import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import 'rxjs/add/operator/toPromise';
import {Message} from 'primeng/components/common/api';
import {MessageService} from 'primeng/components/common/messageservice';
import { environment } from '../../environments/environment';
import { Observable } from 'rxjs/Observable';


@Injectable()
export class PublishTokenService {


  constructor(private http: Http, private messageService: MessageService) { }

  getList() {
    return this.http.get(environment.apiHost+ 'api/publishtoken/All')
    .toPromise()
    .then(res => {
      console.log(res.json());
      return <any[]> res.json();
    })
    .then(data => data);
  }

  save(item:any)  {
    console.log(item);
    return this.http.post(environment.apiHost+'api/publishtoken/Item', item)
    .catch((err: Response) => {
        this.messageService.add({severity: 'error', 
        summary: 'Publish Token Save Erorr', 
        detail: 'Error during save.'  });

        return Observable.throw(new Error("Error saving Publish Token"));
     }).toPromise(). then(data =>{
      this.messageService.add({severity: 'success', summary: 'Publish Token Saved ', detail: 'Publish Token ' + item.reposlug + ' has been saved'
     + JSON.stringify(item)});
      return data[0];
     })   ; 
      
  }

  delete(item:any)  {
    console.log(item);
    return this.http.get('/assets/users.json')
    .toPromise()
    .then(res => <any[]> res.json().rows)
    .then(data => { 
      this.messageService.add({severity:'success', summary:'User deleted', detail: 'User' + item.username + ' has been deleted'});
      return data[0];
    });
    
  }

}
